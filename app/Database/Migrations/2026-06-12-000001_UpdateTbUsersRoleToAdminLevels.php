<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateTbUsersRoleToAdminLevels extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('tb_users') || !$this->db->fieldExists('role', 'tb_users')) {
            return;
        }

        $this->forge->modifyColumn('tb_users', [
            'role' => [
                'name' => 'role',
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'default' => 'admin1',
            ],
        ]);

        $this->db->table('tb_users')
            ->groupStart()
            ->where('role', null)
            ->orWhere('role', '')
            ->orWhere('role', '0')
            ->orWhere('role', 'admin')
            ->groupEnd()
            ->update(['role' => 'admin1']);

        $this->db->table('tb_users')
            ->groupStart()
            ->where('role', '1')
            ->orWhere('role', 'user')
            ->groupEnd()
            ->update(['role' => 'admin3']);
    }

    public function down()
    {
        if (!$this->db->tableExists('tb_users') || !$this->db->fieldExists('role', 'tb_users')) {
            return;
        }

        $this->db->table('tb_users')
            ->where('role', 'admin3')
            ->update(['role' => '1']);

        $this->db->table('tb_users')
            ->whereIn('role', ['admin1', 'admin2'])
            ->update(['role' => '0']);

        $this->forge->modifyColumn('tb_users', [
            'role' => [
                'name' => 'role',
                'type' => 'INT',
                'null' => true,
            ],
        ]);
    }
}
