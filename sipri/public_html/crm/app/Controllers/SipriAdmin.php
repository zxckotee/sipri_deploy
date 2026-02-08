<?php

namespace App\Controllers;

class SipriAdmin extends SipriBaseController {

    public function index() {
        if (!($this->login_user->is_admin || get_array_value($this->login_user->permissions, "sipri_manage"))) {
            app_redirect("forbidden");
        }

        return $this->template->rander("sipri/admin/index");
    }

    public function save_settings() {
        if (!($this->login_user->is_admin || get_array_value($this->login_user->permissions, "sipri_manage"))) {
            app_redirect("forbidden");
        }

        $this->validate_submitted_data([
            "sipri_admin_panel_url" => "permit_empty",
            "sipri_allow_clients" => "permit_empty"
        ]);

        $url = trim($this->request->getPost("sipri_admin_panel_url"));
        $this->Settings_model->save_setting("sipri_admin_panel_url", $url, "app");

        $allow_clients = $this->request->getPost("sipri_allow_clients") ? "1" : "0";
        $this->Settings_model->save_setting("sipri_allow_clients", $allow_clients, "app");

        echo json_encode(["success" => true, "message" => app_lang("record_saved")]);
    }
}


