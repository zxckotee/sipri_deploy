<?php

namespace App\Controllers;

class SipriDepartments extends SipriBaseController {

    protected $Sipri_departments_model;
    protected $Sipri_tenants_model;

    public function __construct() {
        parent::__construct();
        $this->require_sipri_manage();

        $this->Sipri_departments_model = model("App\\Models\\Sipri_departments_model");
        $this->Sipri_tenants_model = model("App\\Models\\Sipri_tenants_model");
    }

    public function index() {
        $tenants = $this->Sipri_tenants_model->get_details(array("only_active" => 1))->getResult();
        $tenant_dropdown = array("" => app_lang("all"));
        foreach ($tenants as $t) {
            $tenant_dropdown[$t->id] = $t->name;
        }

        return $this->template->rander("sipri/admin/departments/index", array(
            "tenant_dropdown" => $tenant_dropdown
        ));
    }

    public function modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "tenant_id" => "numeric"
        ));

        $id = $this->request->getPost("id");
        $tenant_id = $this->request->getPost("tenant_id");
        $model_info = $this->Sipri_departments_model->get_one($id);

        $tenants = $this->Sipri_tenants_model->get_details(array("only_active" => 1))->getResult();
        $tenant_dropdown = array("" => app_lang("select"));
        foreach ($tenants as $t) {
            $tenant_dropdown[$t->id] = $t->name;
        }

        $team_members = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "staff", "status" => "active"))->getResult();
        $members_dropdown = array(array("id" => "", "text" => "-"));
        foreach ($team_members as $m) {
            $members_dropdown[] = array("id" => $m->id, "text" => $m->first_name . " " . $m->last_name);
        }

        // parent departments dropdown within selected tenant (if any)
        $parent_dropdown = array("" => "-");
        $parent_tenant = $model_info && $model_info->tenant_id ? $model_info->tenant_id : $tenant_id;
        if ($parent_tenant) {
            $parents = $this->Sipri_departments_model->get_details(array("tenant_id" => $parent_tenant))->getResult();
            foreach ($parents as $p) {
                if ($model_info && $model_info->id && (int) $p->id === (int) $model_info->id) {
                    continue;
                }
                $parent_dropdown[$p->id] = $p->name;
            }
        }

        return $this->template->view("sipri/admin/departments/modal_form", array(
            "model_info" => $model_info,
            "tenant_dropdown" => $tenant_dropdown,
            "parent_dropdown" => $parent_dropdown,
            "members_dropdown" => json_encode($members_dropdown)
        ));
    }

    public function save() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "tenant_id" => "required|numeric",
            "name" => "required"
        ));

        $id = $this->request->getPost("id");
        $data = array(
            "tenant_id" => $this->request->getPost("tenant_id"),
            "parent_id" => $this->request->getPost("parent_id") ? $this->request->getPost("parent_id") : null,
            "name" => $this->request->getPost("name"),
            "code" => $this->request->getPost("code"),
            "is_active" => $this->request->getPost("is_active") ? 1 : 0,
            "manager_user_id" => $this->request->getPost("manager_user_id") ? $this->request->getPost("manager_user_id") : null,
            "deputy_user_id" => $this->request->getPost("deputy_user_id") ? $this->request->getPost("deputy_user_id") : null,
            "updated_at" => get_current_utc_time()
        );

        if (!$id) {
            $data["created_at"] = get_current_utc_time();
        }

        $data = clean_data($data);
        $save_id = $this->Sipri_departments_model->ci_save($data, $id);

        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), "id" => $save_id, "message" => app_lang("record_saved")));
        } else {
            echo json_encode(array("success" => false, "message" => app_lang("error_occurred")));
        }
    }

    public function delete() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost("id");
        if ($this->request->getPost("undo")) {
            if ($this->Sipri_departments_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => app_lang("record_undone")));
            } else {
                echo json_encode(array("success" => false, "message" => app_lang("error_occurred")));
            }
        } else {
            if ($this->Sipri_departments_model->delete($id)) {
                echo json_encode(array("success" => true, "message" => app_lang("record_deleted")));
            } else {
                echo json_encode(array("success" => false, "message" => app_lang("record_cannot_be_deleted")));
            }
        }
    }

    public function list_data() {
        $tenant_id = $this->request->getGet("tenant_id");
        $options = array();
        if ($tenant_id && is_numeric($tenant_id)) {
            $options["tenant_id"] = $tenant_id;
        }
        $list = $this->Sipri_departments_model->get_details($options)->getResult();
        $result = array();
        foreach ($list as $row) {
            $result[] = $this->_make_row($row);
        }
        echo json_encode(array("data" => $result));
    }

    private function _row_data($id) {
        $row = $this->Sipri_departments_model->get_details(array("id" => $id))->getRow();
        return $this->_make_row($row);
    }

    private function _make_row($row) {
        $active = $row->is_active ? "<span class='badge bg-success'>" . app_lang("active") . "</span>" : "<span class='badge bg-secondary'>" . app_lang("inactive") . "</span>";
        return array(
            $row->tenant_name ? $row->tenant_name : "-",
            $row->name,
            $row->code ? $row->code : "-",
            $row->manager_name ? $row->manager_name : "-",
            $active,
            modal_anchor(get_uri("sipri/departments/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang("edit"), "data-post-id" => $row->id, "data-post-tenant_id" => $row->tenant_id))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array("title" => app_lang("delete"), "class" => "delete", "data-id" => $row->id, "data-action-url" => get_uri("sipri/departments/delete"), "data-action" => "delete"))
        );
    }
}


