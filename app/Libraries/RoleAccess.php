<?php

namespace App\Libraries;

class RoleAccess
{
    private array $actions = ['lihat' => 'Lihat', 'tambah' => 'Tambah', 'edit' => 'Edit', 'hapus' => 'Hapus'];

    private array $menus = [
        'dashboard' => ['menu' => 'Dashboard', 'category' => '-', 'paths' => ['lihat' => ['dashboard']]],
        'anak' => ['menu' => 'Data Anak', 'category' => 'Data Utama', 'paths' => ['lihat' => ['adminanak'], 'edit' => ['admin/updateAnak'], 'hapus' => ['admin/deleteAnak']]],
        'statusgizi' => ['menu' => 'Data Terdahulu', 'category' => 'Data Utama', 'paths' => ['lihat' => ['adminstatusgizi'], 'tambah' => ['admin/uploadStatusGizi']]],
        'hasildiagnosa' => ['menu' => 'Hasil Diagnosa', 'category' => 'Data Utama', 'paths' => ['lihat' => ['adminhasildiagnosa'], 'hapus' => ['admin/deleteHasilDiagnosa']]],
        'users' => ['menu' => 'Data Users', 'category' => 'Data Utama', 'paths' => ['lihat' => ['adminusers'], 'tambah' => ['admin/createUser', 'admin/createRole'], 'edit' => ['admin/updateUser', 'admin/updateRoleAccess'], 'hapus' => ['admin/deleteUser']]],
        'gejala' => ['menu' => 'Data Gejala', 'category' => 'Basis Perhitungan', 'paths' => ['lihat' => ['admingejala'], 'tambah' => ['admin/createGejala'], 'edit' => ['admin/updateGejala'], 'hapus' => ['admin/deleteGejala']]],
        'hipotesis' => ['menu' => 'Data Hipotesis', 'category' => 'Basis Perhitungan', 'paths' => ['lihat' => ['adminhipotesis']]],
        'standar' => ['menu' => 'Standar Antropometri', 'category' => 'Basis Perhitungan', 'paths' => ['lihat' => ['adminstandar'], 'edit' => ['admin/updateStandar']]],
        'rulebased' => ['menu' => 'Rule Based', 'category' => 'Basis Perhitungan', 'paths' => ['lihat' => ['adminrulebased'], 'tambah' => ['admin/createRuleBased'], 'edit' => ['admin/updateRuleBased'], 'hapus' => ['admin/deleteRuleBased']]],
        'prior' => ['menu' => 'Prior Theorema Bayes', 'category' => 'Basis Perhitungan', 'paths' => ['lihat' => ['adminprior'], 'edit' => ['admin/updatePrior']]],
        'likelihood' => ['menu' => 'Probabilitas Antropometri', 'category' => 'Basis Perhitungan', 'paths' => ['lihat' => ['adminlikelihood'], 'edit' => ['admin/updateLikelihood']]],
        'nilaiprobabilitas' => ['menu' => 'Probabilitas Gejala', 'category' => 'Basis Perhitungan', 'paths' => ['lihat' => ['adminnilaiprobabilitas']]],
        'settings' => ['menu' => 'Pengaturan Profil', 'category' => 'Akun', 'paths' => ['lihat' => ['adminsettings'], 'edit' => ['adminsettings']]],
    ];

    private array $defaultRoles = [
        'admin1' => 'Admin 1 - Full akses',
        'admin2' => 'Admin 2 - Data operasional',
        'admin3' => 'Admin 3 - Lihat data',
    ];

    public function actions(): array
    {
        return $this->actions;
    }

    public function menus(): array
    {
        $rows = [];
        foreach ($this->menus as $key => $menu) {
            $rows[] = [
                'key' => $key,
                'menu' => $menu['menu'],
                'category' => $menu['category'],
                'supported_actions' => array_keys($menu['paths']),
            ];
        }

        return $rows;
    }

    public function supportedActions(): array
    {
        $supported = [];
        foreach ($this->menus as $menuKey => $menu) {
            $supported[$menuKey] = array_fill_keys(array_keys($menu['paths']), true);
        }

        return $supported;
    }

    public function roles(): array
    {
        $db = db_connect();
        if (!$db->tableExists('tb_roles')) {
            return $this->defaultRoles;
        }

        $rows = $db->table('tb_roles')
            ->select('role_code, role_name')
            ->orderBy('role_code', 'ASC')
            ->get()
            ->getResultArray();

        $roles = [];
        foreach ($rows as $row) {
            $code = $this->normalizeRole((string) ($row['role_code'] ?? ''));
            if ($code === '') {
                continue;
            }

            $roles[$code] = (string) ($row['role_name'] ?? $code);
        }

        return $roles ?: $this->defaultRoles;
    }

    public function normalizeRole(string $role): string
    {
        $role = strtolower(trim($role));
        if ($role === '1' || $role === 'user') {
            return 'admin3';
        }

        if ($role === '') {
            return 'admin1';
        }

        return preg_replace('/[^a-z0-9_-]+/', '', $role) ?: 'admin1';
    }

    public function permissionMatrix(): array
    {
        $matrix = $this->defaultPermissionMatrix();
        $db = db_connect();

        if (!$db->tableExists('tb_role_permissions')) {
            return $matrix;
        }

        $rows = $db->table('tb_role_permissions')
            ->select('role_code, menu_key, action_key, allowed')
            ->get()
            ->getResultArray();

        foreach ($this->roles() as $role => $label) {
            $matrix[$role] ??= $this->emptyRolePermissions();
        }

        foreach ($rows as $row) {
            $role = $this->normalizeRole((string) ($row['role_code'] ?? ''));
            $menu = (string) ($row['menu_key'] ?? '');
            $action = (string) ($row['action_key'] ?? '');
            if (!isset($this->menus[$menu], $this->actions[$action]) || !$this->isActionSupported($menu, $action)) {
                continue;
            }

            $matrix[$role][$menu][$action] = (bool) ($row['allowed'] ?? false);
        }

        return $matrix;
    }

    public function hasPermission(string $role, string $menu, string $action = 'lihat'): bool
    {
        $role = $this->normalizeRole($role);
        $matrix = $this->permissionMatrix();

        return !empty($matrix[$role][$menu][$action]);
    }

    public function isPathAllowed(string $role, string $path): bool
    {
        $path = trim($path, '/');
        if ($path === 'logout') {
            return true;
        }

        foreach ($this->menus as $menuKey => $menu) {
            foreach ($menu['paths'] as $action => $paths) {
                foreach ($paths as $allowedPath) {
                    if ($path === $allowedPath || str_starts_with($path, $allowedPath . '/')) {
                        return $this->hasPermission($role, $menuKey, $action);
                    }
                }
            }
        }

        return false;
    }

    public function createRole(string $code, string $name): bool
    {
        $db = db_connect();
        if (!$db->tableExists('tb_roles')) {
            return false;
        }

        $code = $this->normalizeRole($code);
        $name = trim($name);
        if ($code === '' || $name === '') {
            return false;
        }

        if ($db->table('tb_roles')->where('role_code', $code)->countAllResults() > 0) {
            return false;
        }

        $db->table('tb_roles')->insert([
            'role_code' => $code,
            'role_name' => $name,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->savePermissions($code, []);

        return true;
    }

    public function savePermissions(string $role, array $permissions): bool
    {
        $db = db_connect();
        if (!$db->tableExists('tb_role_permissions')) {
            return false;
        }

        $role = $this->normalizeRole($role);
        $db->table('tb_role_permissions')->where('role_code', $role)->delete();

        $rows = [];
        foreach ($this->menus as $menuKey => $menu) {
            foreach (array_keys($this->actions) as $action) {
                $supported = $this->isActionSupported($menuKey, $action);
                $rows[] = [
                    'role_code' => $role,
                    'menu_key' => $menuKey,
                    'action_key' => $action,
                    'allowed' => $supported && !empty($permissions[$menuKey][$action]) ? 1 : 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
        }

        if ($rows !== []) {
            $db->table('tb_role_permissions')->insertBatch($rows);
        }

        return true;
    }

    private function defaultPermissionMatrix(): array
    {
        $matrix = [];
        foreach ($this->defaultRoles as $role => $label) {
            $matrix[$role] = $this->emptyRolePermissions();
        }

        foreach (array_keys($this->menus) as $menu) {
            foreach (array_keys($this->actions) as $action) {
                $matrix['admin1'][$menu][$action] = false;
            }
        }

        foreach (['dashboard', 'anak', 'statusgizi', 'hasildiagnosa', 'users', 'gejala', 'hipotesis', 'standar', 'rulebased', 'prior', 'likelihood', 'nilaiprobabilitas', 'settings'] as $menu) {
            $matrix['admin1'][$menu]['lihat'] = true;
        }

        foreach (['anak', 'users', 'gejala', 'rulebased'] as $menu) {
            $matrix['admin1'][$menu]['tambah'] = true;
            $matrix['admin1'][$menu]['edit'] = true;
            $matrix['admin1'][$menu]['hapus'] = true;
        }

        $matrix['admin1']['hasildiagnosa']['hapus'] = true;

        foreach (['statusgizi'] as $menu) {
            $matrix['admin1'][$menu]['tambah'] = true;
        }

        foreach (['standar', 'prior', 'likelihood', 'settings'] as $menu) {
            $matrix['admin1'][$menu]['edit'] = true;
        }

        foreach (['dashboard', 'anak', 'statusgizi', 'hasildiagnosa', 'settings'] as $menu) {
            $matrix['admin2'][$menu]['lihat'] = true;
        }
        $matrix['admin2']['anak']['edit'] = true;
        $matrix['admin2']['anak']['hapus'] = true;
        $matrix['admin2']['statusgizi']['tambah'] = true;
        $matrix['admin2']['settings']['edit'] = true;

        foreach (['dashboard', 'anak', 'statusgizi', 'hasildiagnosa', 'settings'] as $menu) {
            $matrix['admin3'][$menu]['lihat'] = true;
        }
        $matrix['admin3']['settings']['edit'] = true;

        foreach (array_keys($matrix) as $role) {
            foreach (array_keys($this->menus) as $menu) {
                foreach (array_keys($this->actions) as $action) {
                    if (!$this->isActionSupported($menu, $action)) {
                        $matrix[$role][$menu][$action] = false;
                    }
                }
            }
        }

        return $matrix;
    }

    private function isActionSupported(string $menu, string $action): bool
    {
        return isset($this->menus[$menu]['paths'][$action]);
    }

    private function emptyRolePermissions(): array
    {
        $permissions = [];
        foreach (array_keys($this->menus) as $menu) {
            foreach (array_keys($this->actions) as $action) {
                $permissions[$menu][$action] = false;
            }
        }

        return $permissions;
    }
}
