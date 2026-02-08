<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSipriOrgRoleFields extends Migration {

    public function up() {
        // sipri_tenants: director_user_id
        if ($this->db->tableExists("sipri_tenants") && !$this->db->fieldExists("director_user_id", "sipri_tenants")) {
            $this->forge->addColumn("sipri_tenants", [
                "director_user_id" => ["type" => "INT", "constraint" => 11, "unsigned" => true, "null" => true, "after" => "is_active"]
            ]);
        }

        // sipri_departments: manager_user_id / deputy_user_id
        if ($this->db->tableExists("sipri_departments")) {
            $fields = [];
            if (!$this->db->fieldExists("manager_user_id", "sipri_departments")) {
                $fields["manager_user_id"] = ["type" => "INT", "constraint" => 11, "unsigned" => true, "null" => true, "after" => "code"];
            }
            if (!$this->db->fieldExists("deputy_user_id", "sipri_departments")) {
                $fields["deputy_user_id"] = ["type" => "INT", "constraint" => 11, "unsigned" => true, "null" => true, "after" => "manager_user_id"];
            }
            if ($fields) {
                $this->forge->addColumn("sipri_departments", $fields);
            }
        }
    }

    public function down() {
        if ($this->db->tableExists("sipri_tenants") && $this->db->fieldExists("director_user_id", "sipri_tenants")) {
            $this->forge->dropColumn("sipri_tenants", "director_user_id");
        }

        if ($this->db->tableExists("sipri_departments")) {
            $drop = [];
            foreach (["manager_user_id", "deputy_user_id"] as $col) {
                if ($this->db->fieldExists($col, "sipri_departments")) {
                    $drop[] = $col;
                }
            }
            if ($drop) {
                $this->forge->dropColumn("sipri_departments", $drop);
            }
        }
    }
}


