<?php

namespace App\Controllers;

class SipriProfile extends SipriBaseController {

    protected $Sipri_tenants_model;
    protected $Sipri_departments_model;

    public function __construct() {
        parent::__construct();
        $this->Sipri_tenants_model = model("App\\Models\\Sipri_tenants_model");
        $this->Sipri_departments_model = model("App\\Models\\Sipri_departments_model");
    }

    public function index() {
        $tenants = [];
        if (db_connect()->tableExists("sipri_tenants")) {
            $tenants = $this->Sipri_tenants_model->get_all_where(["deleted" => 0, "is_active" => 1])->getResult();
        }

        $departments = [];
        if (db_connect()->tableExists("sipri_departments") && $this->login_user->sipri_tenant_id) {
            $departments = $this->Sipri_departments_model->get_details(["tenant_id" => $this->login_user->sipri_tenant_id])->getResult();
        }

        return $this->template->rander("sipri/profile/index", [
            "tenants" => $tenants,
            "departments" => $departments
        ]);
    }

    public function save() {
        $is_anonymous = $this->request->getPost("sipri_is_anonymous") ? 1 : 0;

        // required fields depend on anonymous mode
        $is_client = ($this->login_user->user_type === "client");
        $rules = [
            "sipri_is_anonymous" => "permit_empty",
            "sipri_nickname" => $is_anonymous ? "required" : "permit_empty",
            // for clients we don't require position/work email (they can be unknown)
            "sipri_position" => ($is_anonymous || $is_client) ? "permit_empty" : "required",
            "sipri_work_email" => ($is_anonymous || $is_client) ? "permit_empty|valid_email" : "required|valid_email",
            "sipri_personal_email" => "permit_empty|valid_email",
            "sipri_tenant_id" => $is_anonymous ? "permit_empty" : "required|numeric",
            // department is optional for clients
            "sipri_department_id" => ($is_anonymous || $is_client) ? "permit_empty|numeric" : "required|numeric",
        ];
        $this->validate_submitted_data($rules);

        $data = [
            "sipri_is_anonymous" => $is_anonymous,
            "sipri_nickname" => $this->request->getPost("sipri_nickname"),
            "sipri_position" => $this->request->getPost("sipri_position"),
            "sipri_work_email" => $this->request->getPost("sipri_work_email"),
            "sipri_personal_email" => $this->request->getPost("sipri_personal_email"),
        ];

        // tenant/department only for non-anonymous
        if (!$is_anonymous) {
            $data["sipri_tenant_id"] = $this->request->getPost("sipri_tenant_id");
            $data["sipri_department_id"] = $this->request->getPost("sipri_department_id");
        } else {
            $data["sipri_tenant_id"] = null;
            $data["sipri_department_id"] = null;
        }

        $profile_completed = $is_anonymous
            ? (bool) $this->request->getPost("sipri_nickname")
            : (
                (bool) $this->request->getPost("sipri_tenant_id")
                && (
                    $is_client
                        ? true
                        : ((bool) $this->request->getPost("sipri_position") && (bool) $this->request->getPost("sipri_work_email") && (bool) $this->request->getPost("sipri_department_id"))
                  )
              );
        $data["sipri_profile_completed"] = $profile_completed ? 1 : 0;

        $data = clean_data($data);
        $this->Users_model->ci_save($data, $this->login_user->id);

        echo json_encode(["success" => true, "message" => app_lang("record_saved")]);
    }

    public function departments_json() {
        $tenant_id = $this->request->getGet("tenant_id");
        if (!$tenant_id || !is_numeric($tenant_id) || !db_connect()->tableExists("sipri_departments")) {
            echo json_encode([]);
            return;
        }

        $rows = $this->Sipri_departments_model->get_details(["tenant_id" => $tenant_id])->getResult();
        $out = [];
        foreach ($rows as $r) {
            $out[] = ["id" => $r->id, "text" => $r->name];
        }
        echo json_encode($out);
    }
}


