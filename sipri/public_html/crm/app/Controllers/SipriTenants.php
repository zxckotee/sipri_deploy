<?php

namespace App\Controllers;

class SipriTenants extends SipriBaseController {

    protected $Sipri_tenants_model;

    public function __construct() {
        parent::__construct();
        $this->require_sipri_manage();

        $this->Sipri_tenants_model = model("App\\Models\\Sipri_tenants_model");
    }

    public function index() {
        return $this->template->rander("sipri/admin/tenants/index");
    }

    public function modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $team_members = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "staff", "status" => "active"))->getResult();
        $members_dropdown = array(array("id" => "", "text" => "-"));
        foreach ($team_members as $m) {
            $members_dropdown[] = array("id" => $m->id, "text" => $m->first_name . " " . $m->last_name);
        }

        $view_data["members_dropdown"] = json_encode($members_dropdown);
        $view_data["model_info"] = $this->Sipri_tenants_model->get_one($this->request->getPost("id"));
        return $this->template->view("sipri/admin/tenants/modal_form", $view_data);
    }

    public function save() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "name" => "required"
        ));

        $id = $this->request->getPost("id");
        $data = array(
            "name" => $this->request->getPost("name"),
            "slug" => $this->request->getPost("slug"),
            "corp_key" => $this->request->getPost("corp_key"),
            "is_active" => $this->request->getPost("is_active") ? 1 : 0,
            "director_user_id" => $this->request->getPost("director_user_id") ? $this->request->getPost("director_user_id") : null,
            "updated_at" => get_current_utc_time()
        );

        if (!$id) {
            $data["created_at"] = get_current_utc_time();
        }

        $data = clean_data($data);
        $save_id = $this->Sipri_tenants_model->ci_save($data, $id);

        // Auto-create SIPRI Knowledge Base default categories for this tenant (if KB tables exist)
        if ($save_id && !$id && db_connect()->tableExists("help_categories")) {
            $db = db_connect();
            $hc = $db->prefixTable("help_categories");
            $exists = $db->query("SELECT id FROM $hc WHERE deleted=0 AND type='sipri_knowledge_base' AND sipri_tenant_id=" . $db->escapeString($save_id) . " LIMIT 1")->getRow();
            if (!$exists) {
                $items = [
                    ["title" => "Нормативные документы", "sort" => 10],
                    ["title" => "Документы предприятия", "sort" => 20],
                    ["title" => "Информационные ресурсы, новости", "sort" => 30],
                    ["title" => "Сайт предприятия", "sort" => 40],
                ];
                foreach ($items as $it) {
                    $db->query("INSERT INTO $hc (title, description, type, sort, status, deleted, sipri_tenant_id, sipri_department_id)
                                VALUES ("
                                . $db->escape($it["title"]) . ", '', 'sipri_knowledge_base', " . (int) $it["sort"] . ", 'active', 0, " . $db->escapeString($save_id) . ", NULL)");
                }
            }
        }

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
            if ($this->Sipri_tenants_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => app_lang("record_undone")));
            } else {
                echo json_encode(array("success" => false, "message" => app_lang("error_occurred")));
            }
        } else {
            if ($this->Sipri_tenants_model->delete($id)) {
                echo json_encode(array("success" => true, "message" => app_lang("record_deleted")));
            } else {
                echo json_encode(array("success" => false, "message" => app_lang("record_cannot_be_deleted")));
            }
        }
    }

    public function list_data() {
        $list = $this->Sipri_tenants_model->get_details()->getResult();
        $result = array();
        foreach ($list as $row) {
            $result[] = $this->_make_row($row);
        }
        echo json_encode(array("data" => $result));
    }

    private function _row_data($id) {
        $row = $this->Sipri_tenants_model->get_details(array("id" => $id))->getRow();
        return $this->_make_row($row);
    }

    private function _make_row($row) {
        $active = $row->is_active ? "<span class='badge bg-success'>" . app_lang("active") . "</span>" : "<span class='badge bg-secondary'>" . app_lang("inactive") . "</span>";
        return array(
            $row->name,
            $row->slug ? $row->slug : "-",
            $row->director_name ? $row->director_name : "-",
            $active,
            modal_anchor(get_uri("sipri/tenants/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang("edit"), "data-post-id" => $row->id))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array("title" => app_lang("delete"), "class" => "delete", "data-id" => $row->id, "data-action-url" => get_uri("sipri/tenants/delete"), "data-action" => "delete"))
        );
    }
}


