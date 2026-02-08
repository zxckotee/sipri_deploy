<?php

namespace App\Controllers;

class SipriDelegations extends SipriBaseController {

    protected $Sipri_delegations_model;
    protected $Sipri_tenants_model;

    public function __construct() {
        parent::__construct();
        $this->require_sipri_manage();

        $this->Sipri_delegations_model = model("App\\Models\\Sipri_delegations_model");
        $this->Sipri_tenants_model = model("App\\Models\\Sipri_tenants_model");
    }

    public function index() {
        $tenants = $this->Sipri_tenants_model->get_details(array("only_active" => 1))->getResult();
        $tenant_dropdown = array("" => app_lang("all"));
        foreach ($tenants as $t) {
            $tenant_dropdown[$t->id] = $t->name;
        }

        return $this->template->rander("sipri/admin/delegations/index", array(
            "tenant_dropdown" => $tenant_dropdown
        ));
    }

    public function modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

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

        $view_data["tenant_dropdown"] = $tenant_dropdown;
        $view_data["members_dropdown"] = json_encode($members_dropdown);
        $view_data["model_info"] = $this->Sipri_delegations_model->get_one($this->request->getPost("id"));

        return $this->template->view("sipri/admin/delegations/modal_form", $view_data);
    }

    public function save() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "tenant_id" => "required|numeric",
            "from_user_id" => "required|numeric",
            "to_user_id" => "required|numeric"
        ));

        $id = $this->request->getPost("id");
        $data = array(
            "tenant_id" => $this->request->getPost("tenant_id"),
            "from_user_id" => $this->request->getPost("from_user_id"),
            "to_user_id" => $this->request->getPost("to_user_id"),
            "starts_at" => $this->request->getPost("starts_at") ? $this->request->getPost("starts_at") : null,
            "ends_at" => $this->request->getPost("ends_at") ? $this->request->getPost("ends_at") : null,
            "is_active" => $this->request->getPost("is_active") ? 1 : 0,
            "created_at" => get_current_utc_time()
        );
        $data = clean_data($data);

        $save_id = $this->Sipri_delegations_model->ci_save($data, $id);
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
            if ($this->Sipri_delegations_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => app_lang("record_undone")));
            } else {
                echo json_encode(array("success" => false, "message" => app_lang("error_occurred")));
            }
        } else {
            if ($this->Sipri_delegations_model->delete($id)) {
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

        $list = $this->Sipri_delegations_model->get_details($options)->getResult();
        $result = array();
        foreach ($list as $row) {
            $result[] = $this->_make_row($row);
        }
        echo json_encode(array("data" => $result));
    }

    private function _row_data($id) {
        $row = $this->Sipri_delegations_model->get_details(array("id" => $id))->getRow();
        return $this->_make_row($row);
    }

    private function _make_row($row) {
        $active = $row->is_active ? "<span class='badge bg-success'>" . app_lang("active") . "</span>" : "<span class='badge bg-secondary'>" . app_lang("inactive") . "</span>";
        return array(
            $row->tenant_name ? $row->tenant_name : "-",
            $row->from_user_name ? $row->from_user_name : "-",
            $row->to_user_name ? $row->to_user_name : "-",
            $row->starts_at ? $row->starts_at : "-",
            $row->ends_at ? $row->ends_at : "-",
            $active,
            modal_anchor(get_uri("sipri/delegations/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang("edit"), "data-post-id" => $row->id))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array("title" => app_lang("delete"), "class" => "delete", "data-id" => $row->id, "data-action-url" => get_uri("sipri/delegations/delete"), "data-action" => "delete"))
        );
    }
}


