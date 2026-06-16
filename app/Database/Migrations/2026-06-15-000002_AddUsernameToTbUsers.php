<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUsernameToTbUsers extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('tb_users')) {
            return;
        }

        if (!$this->db->fieldExists('username', 'tb_users')) {
            $this->forge->addColumn('tb_users', [
                'username' => [
                    'type' => 'VARCHAR',
                    'constraint' => 80,
                    'null' => true,
                    'after' => 'nama',
                ],
            ]);
        }

        $this->backfillUsernames();
        $this->addUsernameIndex();
    }

    public function down()
    {
        if (!$this->db->tableExists('tb_users')) {
            return;
        }

        if ($this->indexExists('tb_users', 'uq_tb_users_username')) {
            $this->db->query('ALTER TABLE `tb_users` DROP INDEX `uq_tb_users_username`');
        }

        if ($this->db->fieldExists('username', 'tb_users')) {
            $this->forge->dropColumn('tb_users', 'username');
        }
    }

    private function backfillUsernames(): void
    {
        $rows = $this->db->table('tb_users')
            ->select('id_users, nama, email, username')
            ->orderBy('id_users', 'ASC')
            ->get()
            ->getResultArray();

        $used = [];
        foreach ($rows as $row) {
            $username = trim((string) ($row['username'] ?? ''));
            if ($username !== '') {
                $used[strtolower($username)] = true;
            }
        }

        foreach ($rows as $row) {
            $current = trim((string) ($row['username'] ?? ''));
            if ($current !== '') {
                continue;
            }

            $id = (int) $row['id_users'];
            $source = trim((string) ($row['email'] ?? ''));
            if ($source !== '' && strpos($source, '@') !== false) {
                $source = substr($source, 0, strpos($source, '@'));
            }

            if ($source === '') {
                $source = trim((string) ($row['nama'] ?? ''));
            }

            $base = $this->normalizeUsername($source);
            if ($base === '') {
                $base = 'admin';
            }

            $username = $base;
            $suffix = 1;
            while (isset($used[strtolower($username)])) {
                $username = $base . $suffix;
                $suffix++;
            }

            $used[strtolower($username)] = true;
            $this->db->table('tb_users')
                ->where('id_users', $id)
                ->update(['username' => $username]);
        }
    }

    private function addUsernameIndex(): void
    {
        if ($this->indexExists('tb_users', 'uq_tb_users_username')) {
            return;
        }

        $this->db->query('ALTER TABLE `tb_users` ADD UNIQUE KEY `uq_tb_users_username` (`username`)');
    }

    private function indexExists(string $table, string $index): bool
    {
        $row = $this->db->query(
            'SELECT COUNT(1) AS total FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ?',
            [$this->db->getDatabase(), $table, $index]
        )->getRowArray();

        return (int) ($row['total'] ?? 0) > 0;
    }

    private function normalizeUsername(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/\s+/', '.', $value) ?? '';
        $value = preg_replace('/[^a-z0-9._-]/', '', $value) ?? '';
        $value = trim($value, '._-');

        return substr($value, 0, 50);
    }
}
