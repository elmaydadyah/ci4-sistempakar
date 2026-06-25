<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameNaiveBayesTablesToTeoremaBayes extends Migration
{
    public function up()
    {
        $this->renameTableIfNeeded('tb_naive_bayes_prior', 'tb_teorema_bayes_prior');
        $this->renameTableIfNeeded('tb_naive_bayes_likelihood', 'tb_teorema_bayes_likelihood');
    }

    public function down()
    {
        $this->renameTableIfNeeded('tb_teorema_bayes_likelihood', 'tb_naive_bayes_likelihood');
        $this->renameTableIfNeeded('tb_teorema_bayes_prior', 'tb_naive_bayes_prior');
    }

    private function renameTableIfNeeded(string $from, string $to): void
    {
        if (!$this->db->tableExists($from) || $this->db->tableExists($to)) {
            return;
        }

        $this->forge->renameTable($from, $to);
    }
}
