<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSipriNotificationSettings extends Migration {

    public function up() {
        if (!$this->db->tableExists("notification_settings")) {
            return;
        }

        $table = $this->db->prefixTable("notification_settings");

        $defaults = [
            [
                "event" => "sipri_idea_created",
                "enable_web" => 1,
                "enable_email" => 0,
                "enable_slack" => 0,
                "notify_to_terms" => "team_members"
            ],
            [
                "event" => "sipri_idea_sent_for_approval",
                "enable_web" => 1,
                "enable_email" => 0,
                "enable_slack" => 0,
                "notify_to_terms" => "sipri_idea_approvers"
            ],
            [
                "event" => "sipri_idea_decided",
                "enable_web" => 1,
                "enable_email" => 0,
                "enable_slack" => 0,
                "notify_to_terms" => "sipri_idea_author"
            ],
            [
                "event" => "sipri_idea_commented",
                "enable_web" => 1,
                "enable_email" => 0,
                "enable_slack" => 0,
                "notify_to_terms" => "sipri_idea_author,sipri_idea_approvers"
            ],
        ];

        foreach ($defaults as $d) {
            $event = $this->db->escapeString($d["event"]);
            $exists = $this->db->query("SELECT id FROM $table WHERE event='$event' LIMIT 1")->getRow();
            if ($exists && $exists->id) {
                continue;
            }

            $this->db->table($table)->insert([
                "event" => $d["event"],
                "enable_web" => $d["enable_web"],
                "enable_email" => $d["enable_email"],
                "enable_slack" => $d["enable_slack"],
                "notify_to_terms" => $d["notify_to_terms"],
                "notify_to_team" => "",
                "notify_to_team_members" => "",
                "deleted" => 0,
            ]);
        }
    }

    public function down() {
        if (!$this->db->tableExists("notification_settings")) {
            return;
        }

        $table = $this->db->prefixTable("notification_settings");
        $events = [
            "sipri_idea_created",
            "sipri_idea_sent_for_approval",
            "sipri_idea_decided",
            "sipri_idea_commented",
        ];

        foreach ($events as $e) {
            $e = $this->db->escapeString($e);
            $this->db->query("DELETE FROM $table WHERE event='$e'");
        }
    }
}



