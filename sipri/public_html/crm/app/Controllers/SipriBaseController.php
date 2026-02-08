<?php

namespace App\Controllers;

class SipriBaseController extends Security_Controller {

    public function __construct() {
        parent::__construct();
        $this->require_sipri_access();
        $this->require_sipri_profile_completed();
    }

    protected function require_sipri_access() {
        // staff users: require explicit sipri_access (or admin)
        if ($this->login_user->user_type === "staff") {
            if (!($this->login_user->is_admin || get_array_value($this->login_user->permissions, "sipri_access"))) {
                app_redirect("forbidden");
            }
            return;
        }

        // client users: allow only if enabled and tenant is set (to scope access)
        if ($this->login_user->user_type === "client") {
            if (get_setting("sipri_allow_clients") != "1" || !$this->login_user->sipri_tenant_id) {
                app_redirect("forbidden");
            }
            return;
        }

        // other types are not allowed by default
        if (!$this->login_user->is_admin) {
            app_redirect("forbidden");
        }
    }

    protected function require_sipri_manage() {
        if (!($this->login_user->is_admin || get_array_value($this->login_user->permissions, "sipri_manage"))) {
            app_redirect("forbidden");
        }
    }

    protected function require_sipri_approve() {
        if (!($this->login_user->is_admin || get_array_value($this->login_user->permissions, "sipri_approve"))) {
            app_redirect("forbidden");
        }
    }

    protected function has_sipri_secret_access() {
        return ($this->login_user->is_admin || get_array_value($this->login_user->permissions, "sipri_secret_access"));
    }

    protected function require_sipri_profile_completed() {
        // Allow access to profile page itself even if incomplete
        $router = service('router');
        $controller = strtolower(get_actual_controller_name($router));
        if ($controller === "sipriprofile") {
            return;
        }

        // Only enforce inside SIPRI module
        if (get_setting("module_sipri") && !$this->login_user->sipri_profile_completed && !$this->login_user->is_admin) {
            app_redirect("sipri/profile");
        }
    }
}


