<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSipriAclToKnowledgeBase extends Migration {

    public function up() {
        if (!$this->db->tableExists("help_categories")) {
            return;
        }

        $fields = [];
        if (!$this->db->fieldExists("sipri_tenant_id", "help_categories")) {
            $fields["sipri_tenant_id"] = ["type" => "INT", "constraint" => 11, "unsigned" => true, "null" => true];
        }
        if (!$this->db->fieldExists("sipri_department_id", "help_categories")) {
            $fields["sipri_department_id"] = ["type" => "INT", "constraint" => 11, "unsigned" => true, "null" => true];
        }

        if ($fields) {
            $this->forge->addColumn("help_categories", $fields);
        }
    }

    public function down() {
        if (!$this->db->tableExists("help_categories")) {
            return;
        }

        $drop = [];
        foreach (["sipri_tenant_id", "sipri_department_id"] as $col) {
            if ($this->db->fieldExists($col, "help_categories")) {
                $drop[] = $col;
            }
        }

        if ($drop) {
            $this->forge->dropColumn("help_categories", $drop);
        }
    }
}


