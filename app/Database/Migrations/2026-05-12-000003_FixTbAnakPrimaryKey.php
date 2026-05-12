<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixTbAnakPrimaryKey extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('tb_anak') || !$this->db->fieldExists('id_anak', 'tb_anak')) {
            return;
        }

        $foreignKeys = $this->getAnakForeignKeys();
        $this->dropForeignKeys($foreignKeys);

        $this->db->query(
            'UPDATE tb_anak
             SET id_anak = (
                SELECT next_id
                FROM (
                    SELECT COALESCE(MAX(id_anak), 0) + 1 AS next_id
                    FROM tb_anak
                    WHERE id_anak <> 0
                ) AS next_value
             )
             WHERE id_anak = 0'
        );

        $this->db->query('ALTER TABLE tb_anak MODIFY id_anak INT(11) UNSIGNED NOT NULL AUTO_INCREMENT');

        $this->restoreForeignKeys($foreignKeys);
    }

    public function down()
    {
        if (!$this->db->tableExists('tb_anak') || !$this->db->fieldExists('id_anak', 'tb_anak')) {
            return;
        }

        $foreignKeys = $this->getAnakForeignKeys();
        $this->dropForeignKeys($foreignKeys);
        $this->db->query('ALTER TABLE tb_anak MODIFY id_anak INT(11) UNSIGNED NOT NULL');
        $this->restoreForeignKeys($foreignKeys);
    }

    private function getAnakForeignKeys(): array
    {
        $database = $this->db->database;

        return $this->db->query(
            "SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ?
             AND REFERENCED_TABLE_NAME = 'tb_anak'
             AND REFERENCED_COLUMN_NAME = 'id_anak'",
            [$database]
        )->getResultArray();
    }

    private function dropForeignKeys(array $foreignKeys): void
    {
        foreach ($foreignKeys as $foreignKey) {
            $table = $this->db->escapeIdentifiers($foreignKey['TABLE_NAME']);
            $constraint = $this->db->escapeIdentifiers($foreignKey['CONSTRAINT_NAME']);
            $this->db->query("ALTER TABLE {$table} DROP FOREIGN KEY {$constraint}");
        }
    }

    private function restoreForeignKeys(array $foreignKeys): void
    {
        foreach ($foreignKeys as $foreignKey) {
            $tableName = $foreignKey['TABLE_NAME'];
            $columnName = $foreignKey['COLUMN_NAME'];

            if (!$this->db->tableExists($tableName) || !$this->db->fieldExists($columnName, $tableName)) {
                continue;
            }

            $table = $this->db->escapeIdentifiers($tableName);
            $column = $this->db->escapeIdentifiers($columnName);
            $constraint = $this->db->escapeIdentifiers($foreignKey['CONSTRAINT_NAME']);
            $this->db->query("ALTER TABLE {$table} MODIFY {$column} INT(11) UNSIGNED NULL");
            $this->db->query("ALTER TABLE {$table} ADD CONSTRAINT {$constraint} FOREIGN KEY ({$column}) REFERENCES tb_anak(id_anak) ON DELETE SET NULL ON UPDATE CASCADE");
        }
    }
}
