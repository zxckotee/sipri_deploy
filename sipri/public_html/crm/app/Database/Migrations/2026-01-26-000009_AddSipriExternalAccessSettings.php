<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSipriExternalAccessSettings extends Migration {

    public function up() {
        if (!$this->db->tableExists("settings")) {
            return;
        }

        $settings = $this->db->prefixTable("settings");
        $exists = $this->db->query("SELECT setting_name FROM $settings WHERE setting_name='sipri_allow_clients' AND deleted=0 LIMIT 1")->getRow();
        if (!$exists) {
            $this->db->query("INSERT INTO $settings (setting_name, setting_value, type, deleted) VALUES ('sipri_allow_clients','0','app',0)");
        }
    }

    public function down() {
        if (!$this->db->tableExists("settings")) {
            return;
        }

        $settings = $this->db->prefixTable("settings");
        $this->db->query("DELETE FROM $settings WHERE setting_name='sipri_allow_clients'");
    }
}


