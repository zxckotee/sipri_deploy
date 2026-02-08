<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSipriCoreTables extends Migration {

    public function up() {
        // Tenants (Enterprises)
        if (!$this->db->tableExists("sipri_tenants")) {
            $this->forge->addField([
                "id" => ["type" => "INT", "constraint" => 11, "unsigned" => true, "auto_increment" => true],
                "name" => ["type" => "VARCHAR", "constraint" => 255],
                "slug" => ["type" => "VARCHAR", "constraint" => 128, "null" => true],
                "is_active" => ["type" => "TINYINT", "constraint" => 1, "default" => 1],
                "created_at" => ["type" => "DATETIME", "null" => true],
                "updated_at" => ["type" => "DATETIME", "null" => true],
                "deleted" => ["type" => "TINYINT", "constraint" => 1, "default" => 0],
            ]);
            $this->forge->addKey("id", true);
            $this->forge->addKey("slug");
            $this->forge->createTable("sipri_tenants");
        }

        // Departments (Teams/Groups) with hierarchy
        if (!$this->db->tableExists("sipri_departments")) {
            $this->forge->addField([
                "id" => ["type" => "INT", "constraint" => 11, "unsigned" => true, "auto_increment" => true],
                "tenant_id" => ["type" => "INT", "constraint" => 11, "unsigned" => true],
                "parent_id" => ["type" => "INT", "constraint" => 11, "unsigned" => true, "null" => true],
                "name" => ["type" => "VARCHAR", "constraint" => 255],
                "code" => ["type" => "VARCHAR", "constraint" => 64, "null" => true],
                "is_active" => ["type" => "TINYINT", "constraint" => 1, "default" => 1],
                "created_at" => ["type" => "DATETIME", "null" => true],
                "updated_at" => ["type" => "DATETIME", "null" => true],
                "deleted" => ["type" => "TINYINT", "constraint" => 1, "default" => 0],
            ]);
            $this->forge->addKey("id", true);
            $this->forge->addKey(["tenant_id", "parent_id"]);
            $this->forge->createTable("sipri_departments");
        }

        // Core Idea/Card
        if (!$this->db->tableExists("sipri_ideas")) {
            $this->forge->addField([
                "id" => ["type" => "INT", "constraint" => 11, "unsigned" => true, "auto_increment" => true],
                "tenant_id" => ["type" => "INT", "constraint" => 11, "unsigned" => true],
                "department_id" => ["type" => "INT", "constraint" => 11, "unsigned" => true, "null" => true],
                "category" => ["type" => "VARCHAR", "constraint" => 32, "default" => "enterprise"], // enterprise|client|partner
                "title" => ["type" => "VARCHAR", "constraint" => 255],
                "problem" => ["type" => "TEXT", "null" => true],
                "idea" => ["type" => "TEXT", "null" => true],
                "how_to" => ["type" => "TEXT", "null" => true],
                "resources" => ["type" => "TEXT", "null" => true],
                "status" => ["type" => "VARCHAR", "constraint" => 32, "default" => "discussion"], // discussion|on_approval|approved|in_progress|done_success|done_fail|archived|rejected
                "rating_percent" => ["type" => "INT", "constraint" => 11, "default" => 0],
                "is_anonymous" => ["type" => "TINYINT", "constraint" => 1, "default" => 0],
                "is_secret" => ["type" => "TINYINT", "constraint" => 1, "default" => 0],
                "created_by" => ["type" => "INT", "constraint" => 11, "unsigned" => true],
                "created_at" => ["type" => "DATETIME", "null" => true],
                "updated_at" => ["type" => "DATETIME", "null" => true],
                "deleted" => ["type" => "TINYINT", "constraint" => 1, "default" => 0],
            ]);
            $this->forge->addKey("id", true);
            $this->forge->addKey(["tenant_id", "department_id"]);
            $this->forge->addKey(["status", "category"]);
            $this->forge->createTable("sipri_ideas");
        }

        // Idea votes
        if (!$this->db->tableExists("sipri_idea_votes")) {
            $this->forge->addField([
                "id" => ["type" => "INT", "constraint" => 11, "unsigned" => true, "auto_increment" => true],
                "idea_id" => ["type" => "INT", "constraint" => 11, "unsigned" => true],
                "user_id" => ["type" => "INT", "constraint" => 11, "unsigned" => true],
                "vote" => ["type" => "VARCHAR", "constraint" => 16], // yes|no|abstain
                "created_at" => ["type" => "DATETIME", "null" => true],
            ]);
            $this->forge->addKey("id", true);
            $this->forge->addKey(["idea_id", "user_id"]);
            $this->forge->createTable("sipri_idea_votes");
        }

        // Idea comments / discussion
        if (!$this->db->tableExists("sipri_idea_comments")) {
            $this->forge->addField([
                "id" => ["type" => "INT", "constraint" => 11, "unsigned" => true, "auto_increment" => true],
                "idea_id" => ["type" => "INT", "constraint" => 11, "unsigned" => true],
                "user_id" => ["type" => "INT", "constraint" => 11, "unsigned" => true, "null" => true],
                "is_anonymous" => ["type" => "TINYINT", "constraint" => 1, "default" => 0],
                "comment" => ["type" => "TEXT"],
                "created_at" => ["type" => "DATETIME", "null" => true],
                "deleted" => ["type" => "TINYINT", "constraint" => 1, "default" => 0],
            ]);
            $this->forge->addKey("id", true);
            $this->forge->addKey("idea_id");
            $this->forge->createTable("sipri_idea_comments");
        }

        // Approval chain entries
        if (!$this->db->tableExists("sipri_idea_approvals")) {
            $this->forge->addField([
                "id" => ["type" => "INT", "constraint" => 11, "unsigned" => true, "auto_increment" => true],
                "idea_id" => ["type" => "INT", "constraint" => 11, "unsigned" => true],
                "approver_user_id" => ["type" => "INT", "constraint" => 11, "unsigned" => true],
                "decision" => ["type" => "VARCHAR", "constraint" => 16, "null" => true], // approved|rejected|null
                "comment" => ["type" => "TEXT", "null" => true],
                "decided_at" => ["type" => "DATETIME", "null" => true],
                "created_at" => ["type" => "DATETIME", "null" => true],
            ]);
            $this->forge->addKey("id", true);
            $this->forge->addKey(["idea_id", "approver_user_id"]);
            $this->forge->createTable("sipri_idea_approvals");
        }

        // Idea attachments (stored similarly to other modules: serialized file meta)
        if (!$this->db->tableExists("sipri_idea_attachments")) {
            $this->forge->addField([
                "id" => ["type" => "INT", "constraint" => 11, "unsigned" => true, "auto_increment" => true],
                "idea_id" => ["type" => "INT", "constraint" => 11, "unsigned" => true],
                "uploaded_by" => ["type" => "INT", "constraint" => 11, "unsigned" => true, "null" => true],
                "files" => ["type" => "LONGTEXT", "null" => true], // serialized array
                "created_at" => ["type" => "DATETIME", "null" => true],
                "deleted" => ["type" => "TINYINT", "constraint" => 1, "default" => 0],
            ]);
            $this->forge->addKey("id", true);
            $this->forge->addKey("idea_id");
            $this->forge->createTable("sipri_idea_attachments");
        }

        // Status history
        if (!$this->db->tableExists("sipri_idea_status_history")) {
            $this->forge->addField([
                "id" => ["type" => "INT", "constraint" => 11, "unsigned" => true, "auto_increment" => true],
                "idea_id" => ["type" => "INT", "constraint" => 11, "unsigned" => true],
                "from_status" => ["type" => "VARCHAR", "constraint" => 32, "null" => true],
                "to_status" => ["type" => "VARCHAR", "constraint" => 32],
                "changed_by" => ["type" => "INT", "constraint" => 11, "unsigned" => true, "null" => true],
                "comment" => ["type" => "TEXT", "null" => true],
                "created_at" => ["type" => "DATETIME", "null" => true],
            ]);
            $this->forge->addKey("id", true);
            $this->forge->addKey("idea_id");
            $this->forge->createTable("sipri_idea_status_history");
        }

        // Delegations (acting on behalf)
        if (!$this->db->tableExists("sipri_delegations")) {
            $this->forge->addField([
                "id" => ["type" => "INT", "constraint" => 11, "unsigned" => true, "auto_increment" => true],
                "tenant_id" => ["type" => "INT", "constraint" => 11, "unsigned" => true],
                "from_user_id" => ["type" => "INT", "constraint" => 11, "unsigned" => true],
                "to_user_id" => ["type" => "INT", "constraint" => 11, "unsigned" => true],
                "starts_at" => ["type" => "DATETIME", "null" => true],
                "ends_at" => ["type" => "DATETIME", "null" => true],
                "is_active" => ["type" => "TINYINT", "constraint" => 1, "default" => 1],
                "created_at" => ["type" => "DATETIME", "null" => true],
                "deleted" => ["type" => "TINYINT", "constraint" => 1, "default" => 0],
            ]);
            $this->forge->addKey("id", true);
            $this->forge->addKey(["tenant_id", "from_user_id", "to_user_id"]);
            $this->forge->createTable("sipri_delegations");
        }

        // Secret access locks / whitelist
        if (!$this->db->tableExists("sipri_idea_access_locks")) {
            $this->forge->addField([
                "id" => ["type" => "INT", "constraint" => 11, "unsigned" => true, "auto_increment" => true],
                "idea_id" => ["type" => "INT", "constraint" => 11, "unsigned" => true],
                "user_id" => ["type" => "INT", "constraint" => 11, "unsigned" => true],
                "created_at" => ["type" => "DATETIME", "null" => true],
            ]);
            $this->forge->addKey("id", true);
            $this->forge->addKey(["idea_id", "user_id"]);
            $this->forge->createTable("sipri_idea_access_locks");
        }
    }

    public function down() {
        foreach ([
            "sipri_idea_access_locks",
            "sipri_delegations",
            "sipri_idea_status_history",
            "sipri_idea_attachments",
            "sipri_idea_approvals",
            "sipri_idea_comments",
            "sipri_idea_votes",
            "sipri_ideas",
            "sipri_departments",
            "sipri_tenants",
        ] as $table) {
            if ($this->db->tableExists($table)) {
                $this->forge->dropTable($table, true);
            }
        }
    }
}


