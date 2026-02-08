<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSipriExecutionFields extends Migration {

    public function up() {
        if (!$this->db->tableExists("sipri_ideas")) {
            return;
        }

        $fields = [];
        if (!$this->db->fieldExists("executor_user_id", "sipri_ideas")) {
            $fields["executor_user_id"] = ["type" => "INT", "constraint" => 11, "unsigned" => true, "null" => true, "after" => "status"];
        }
        if (!$this->db->fieldExists("execution_report", "sipri_ideas")) {
            $fields["execution_report"] = ["type" => "TEXT", "null" => true, "after" => "executor_user_id"];
        }
        if (!$this->db->fieldExists("executed_by", "sipri_ideas")) {
            $fields["executed_by"] = ["type" => "INT", "constraint" => 11, "unsigned" => true, "null" => true, "after" => "execution_report"];
        }
        if (!$this->db->fieldExists("executed_at", "sipri_ideas")) {
            $fields["executed_at"] = ["type" => "DATETIME", "null" => true, "after" => "executed_by"];
        }

        if ($fields) {
            $this->forge->addColumn("sipri_ideas", $fields);
        }
    }

    public function down() {
        if (!$this->db->tableExists("sipri_ideas")) {
            return;
        }

        $drop = [];
        foreach (["executor_user_id", "execution_report", "executed_by", "executed_at"] as $col) {
            if ($this->db->fieldExists($col, "sipri_ideas")) {
                $drop[] = $col;
            }
        }
        if ($drop) {
            $this->forge->dropColumn("sipri_ideas", $drop);
        }
    }
}


