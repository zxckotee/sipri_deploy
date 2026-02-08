<?php

namespace App\Controllers;

class SipriInstaller extends Security_Controller {

    public function __construct() {
        parent::__construct();
        $this->access_only_admin_or_settings_admin();
    }

    /**
     * Run CI4 migrations to latest.
     * This app doesn't ship with spark in repo, so we provide a safe admin-only endpoint.
     */
    public function migrate() {
        if (ENVIRONMENT === "production" && !get_setting("allow_sipri_migrations_in_production")) {
            app_redirect("forbidden");
        }

        $migrator = \Config\Services::migrations();

        try {
            $migrator->latest();
            echo json_encode(["success" => true, "message" => "Migrations completed"]);
        } catch (\Throwable $e) {
            log_message('error', '[SIPRI MIGRATION ERROR] {exception}', ['exception' => $e]);
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    }
}


