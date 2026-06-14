<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminRoleFilter implements FilterInterface
{
    private array $rolePermissions = [
        'admin1' => ['*'],
        'admin2' => [
            'dashboard',
            'adminsettings',
            'adminanak',
            'admin/updateAnak',
            'admin/deleteAnak',
            'adminhasildiagnosa',
            'adminstatusgizi',
            'admin/uploadStatusGizi',
        ],
        'admin3' => [
            'dashboard',
            'adminsettings',
            'adminanak',
            'adminhasildiagnosa',
            'adminstatusgizi',
        ],
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        $role = $this->normalizeRole((string) (session()->get('role') ?? 'admin1'));
        session()->set('role', $role);

        if ($this->isAllowed($role, trim($request->getUri()->getPath(), '/'))) {
            return null;
        }

        return redirect()->to('/dashboard')->with('error', 'Role admin Anda tidak memiliki akses ke menu atau aksi tersebut.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }

    private function isAllowed(string $role, string $path): bool
    {
        $permissions = $this->rolePermissions[$role] ?? $this->rolePermissions['admin3'];
        if (in_array('*', $permissions, true)) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($path === $permission || str_starts_with($path, $permission . '/')) {
                return true;
            }
        }

        return false;
    }

    private function normalizeRole(string $role): string
    {
        return match (strtolower(trim($role))) {
            'admin2' => 'admin2',
            'admin3', '1', 'user' => 'admin3',
            default => 'admin1',
        };
    }
}
