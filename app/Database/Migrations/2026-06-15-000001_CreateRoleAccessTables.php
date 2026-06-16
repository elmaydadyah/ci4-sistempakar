<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use App\Libraries\RoleAccess;

class CreateRoleAccessTables extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('tb_users') && $this->db->fieldExists('role', 'tb_users')) {
            $this->forge->modifyColumn('tb_users', [
                'role' => [
                    'name' => 'role',
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'default' => 'admin1',
                    'null' => true,
                ],
            ]);
        }

        if (!$this->db->tableExists('tb_roles')) {
            $this->forge->addField([
                'role_code' => ['type' => 'VARCHAR', 'constraint' => 50],
                'role_name' => ['type' => 'VARCHAR', 'constraint' => 120],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('role_code', true);
            $this->forge->createTable('tb_roles', true);
        }

        if (!$this->db->tableExists('tb_role_permissions')) {
            $this->forge->addField([
                'id_role_permission' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'role_code' => ['type' => 'VARCHAR', 'constraint' => 50],
                'menu_key' => ['type' => 'VARCHAR', 'constraint' => 80],
                'action_key' => ['type' => 'VARCHAR', 'constraint' => 30],
                'allowed' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id_role_permission', true);
            $this->forge->addUniqueKey(['role_code', 'menu_key', 'action_key'], 'uq_role_menu_action');
            $this->forge->createTable('tb_role_permissions', true);
        }

        $roleAccess = new RoleAccess();
        foreach ($roleAccess->roles() as $code => $name) {
            if ($this->db->table('tb_roles')->where('role_code', $code)->countAllResults() === 0) {
                $this->db->table('tb_roles')->insert([
                    'role_code' => $code,
                    'role_name' => $name,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $matrix = $roleAccess->permissionMatrix();
        foreach ($matrix as $role => $permissions) {
            $roleAccess->savePermissions($role, $permissions);
        }
    }

    public function down()
    {
        $this->forge->dropTable('tb_role_permissions', true);
        $this->forge->dropTable('tb_roles', true);
    }
}
