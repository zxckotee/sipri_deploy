<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSipriFieldsToUsers extends Migration {

    public function up() {
        if (!$this->db->tableExists("users")) {
            return;
        }

        $fields = [];

        if (!$this->db->fieldExists("sipri_tenant_id", "users")) {
            $fields["sipri_tenant_id"] = ["type" => "INT", "constraint" => 11, "unsigned" => true, "null" => true];
        }
        if (!$this->db->fieldExists("sipri_department_id", "users")) {
            $fields["sipri_department_id"] = ["type" => "INT", "constraint" => 11, "unsigned" => true, "null" => true];
        }
        if (!$this->db->fieldExists("sipri_is_anonymous", "users")) {
            $fields["sipri_is_anonymous"] = ["type" => "TINYINT", "constraint" => 1, "default" => 0];
        }
        if (!$this->db->fieldExists("sipri_nickname", "users")) {
            $fields["sipri_nickname"] = ["type" => "VARCHAR", "constraint" => 64, "null" => true];
        }
        if (!$this->db->fieldExists("sipri_position", "users")) {
            $fields["sipri_position"] = ["type" => "VARCHAR", "constraint" => 255, "null" => true];
        }
        if (!$this->db->fieldExists("sipri_work_email", "users")) {
            $fields["sipri_work_email"] = ["type" => "VARCHAR", "constraint" => 255, "null" => true];
        }
        if (!$this->db->fieldExists("sipri_personal_email", "users")) {
            $fields["sipri_personal_email"] = ["type" => "VARCHAR", "constraint" => 255, "null" => true];
        }
        if (!$this->db->fieldExists("sipri_profile_completed", "users")) {
            $fields["sipri_profile_completed"] = ["type" => "TINYINT", "constraint" => 1, "default" => 0];
        }

        if ($fields) {
            $this->forge->addColumn("users", $fields);
        }
    }

    public function down() {
        if (!$this->db->tableExists("users")) {
            return;
        }

        $drop = [];
        foreach ([
            "sipri_tenant_id",
            "sipri_department_id",
            "sipri_is_anonymous",
            "sipri_nickname",
            "sipri_position",
            "sipri_work_email",
            "sipri_personal_email",
            "sipri_profile_completed",
        ] as $col) {
            if ($this->db->fieldExists($col, "users")) {
                $drop[] = $col;
            }
        }

        if ($drop) {
            $this->forge->dropColumn("users", $drop);
        }
    }
}


