<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RestoreTbKasusAnakForeignKey extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('tb_kasus') || !$this->db->tableExists('tb_anak')) {
            return;
        }

        if (!$this->db->fieldExists('id_anak', 'tb_kasus') || !$this->db->fieldExists('id_anak', 'tb_anak')) {
            return;
        }

        $this->db->query(
            'UPDATE tb_kasus
             SET id_anak = NULL
             WHERE id_anak IS NOT NULL
             AND id_anak NOT IN (SELECT id_anak FROM tb_anak)'
        );
        $this->db->query('ALTER TABLE tb_kasus MODIFY id_anak INT(11) UNSIGNED NULL');

        if ($this->hasForeignKey('tb_kasus', 'FK_tb_kasus_tb_anak')) {
            return;
        }

        try {
            $this->db->query('ALTER TABLE tb_kasus DROP INDEX FK_tb_kasus_tb_anak');
        } catch (\Throwable $exception) {
            // The index is optional. MySQL will create one for the foreign key if needed.
        }

        $this->db->query('ALTER TABLE tb_kasus ADD CONSTRAINT FK_tb_kasus_tb_anak FOREIGN KEY (id_anak) REFERENCES tb_anak(id_anak) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        if (!$this->db->tableExists('tb_kasus') || !$this->db->fieldExists('id_anak', 'tb_kasus')) {
            return;
        }

        if ($this->hasForeignKey('tb_kasus', 'FK_tb_kasus_tb_anak')) {
            $this->db->query('ALTER TABLE tb_kasus DROP FOREIGN KEY FK_tb_kasus_tb_anak');
        }

        $this->db->query('ALTER TABLE tb_kasus MODIFY id_anak INT(11) NULL');
    }

    private function hasForeignKey(string $table, string $constraint): bool
    {
        $result = $this->db->query(
            'SELECT CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ?
             AND TABLE_NAME = ?
             AND CONSTRAINT_NAME = ?
             AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$this->db->database, $table, $constraint]
        )->getRowArray();

        return $result !== null;
    }
}
