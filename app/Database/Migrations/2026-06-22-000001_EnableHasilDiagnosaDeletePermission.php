<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnableHasilDiagnosaDeletePermission extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('tb_role_permissions')) {
            return;
        }

        $data = [
            'role_code' => 'admin1',
            'menu_key' => 'hasildiagnosa',
            'action_key' => 'hapus',
            'allowed' => 1,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $exists = $this->db->table('tb_role_permissions')
            ->where('role_code', 'admin1')
            ->where('menu_key', 'hasildiagnosa')
            ->where('action_key', 'hapus')
            ->countAllResults() > 0;

        if ($exists) {
            $this->db->table('tb_role_permissions')
                ->where('role_code', 'admin1')
                ->where('menu_key', 'hasildiagnosa')
                ->where('action_key', 'hapus')
                ->update([
                    'allowed' => 1,
                    'updated_at' => $data['updated_at'],
                ]);
            return;
        }

        $this->db->table('tb_role_permissions')->insert($data);
    }

    public function down()
    {
        if (!$this->db->tableExists('tb_role_permissions')) {
            return;
        }

        $this->db->table('tb_role_permissions')
            ->where('role_code', 'admin1')
            ->where('menu_key', 'hasildiagnosa')
            ->where('action_key', 'hapus')
            ->update([
                'allowed' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }
}
