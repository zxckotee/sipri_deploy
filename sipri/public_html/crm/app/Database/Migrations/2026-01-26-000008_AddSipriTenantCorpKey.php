<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSipriTenantCorpKey extends Migration {

    public function up() {
        if ($this->db->tableExists("sipri_tenants") && !$this->db->fieldExists("corp_key", "sipri_tenants")) {
            $this->forge->addColumn("sipri_tenants", [
                "corp_key" => ["type" => "VARCHAR", "constraint" => 64, "null" => true, "after" => "slug"]
            ]);
        }
    }

    public function down() {
        if ($this->db->tableExists("sipri_tenants") && $this->db->fieldExists("corp_key", "sipri_tenants")) {
            $this->forge->dropColumn("sipri_tenants", "corp_key");
        }
    }
}


