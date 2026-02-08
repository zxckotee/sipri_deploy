<?php

namespace App\Controllers;

class SipriIdeas extends SipriBaseController {

    protected $Sipri_ideas_model;
    protected $Sipri_idea_votes_model;
    protected $Sipri_idea_comments_model;
    protected $Sipri_idea_attachments_model;
    protected $Sipri_idea_access_locks_model;
    protected $Sipri_idea_approvals_model;
    protected $Sipri_idea_status_history_model;

    public function __construct() {
        parent::__construct();

        $this->Sipri_ideas_model = model("App\Models\Sipri_ideas_model");
        $this->Sipri_idea_votes_model = model("App\Models\Sipri_idea_votes_model");
        $this->Sipri_idea_comments_model = model("App\Models\Sipri_idea_comments_model");
        $this->Sipri_idea_attachments_model = model("App\Models\Sipri_idea_attachments_model");
        $this->Sipri_idea_access_locks_model = model("App\Models\Sipri_idea_access_locks_model");
        $this->Sipri_idea_approvals_model = model("App\Models\Sipri_idea_approvals_model");
        $this->Sipri_idea_status_history_model = model("App\Models\Sipri_idea_status_history_model");
    }

    public function index() {
        $tenant_id = $this->login_user->sipri_tenant_id;
        $options = array(
            "tenant_id" => $tenant_id,
            "search" => $this->request->getGet("search")
        );

        if ($this->login_user->user_type === "client") {
            $options["category"] = "client";
        }

        $view_data["ideas"] = $this->Sipri_ideas_model->get_details($options)->getResult();
        $view_data["search"] = clean_data($this->request->getGet("search"));

        return $this->template->rander("sipri/ideas/index", $view_data);
    }

    public function modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $id = $this->request->getPost("id");
        $view_data["model_info"] = $this->Sipri_ideas_model->get_one($id);
        return $this->template->view("sipri/ideas/modal_form", $view_data);
    }

    public function save() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required",
            "category" => "required"
        ));

        $id = $this->request->getPost("id");

        $category = $this->request->getPost("category");
        if ($this->login_user->user_type === "client") {
            $category = "client";
        }

        $data = array(
            "tenant_id" => $this->login_user->sipri_tenant_id ? $this->login_user->sipri_tenant_id : 0,
            "department_id" => $this->login_user->sipri_department_id ? $this->login_user->sipri_department_id : null,
            "category" => $category,
            "title" => $this->request->getPost("title"),
            "problem" => $this->request->getPost("problem"),
            "idea" => $this->request->getPost("idea"),
            "how_to" => $this->request->getPost("how_to"),
            "resources" => $this->request->getPost("resources"),
            "is_anonymous" => $this->request->getPost("is_anonymous") ? 1 : 0,
            "is_secret" => ($this->login_user->user_type === "client") ? 0 : ($this->request->getPost("is_secret") ? 1 : 0),
            "updated_at" => get_current_utc_time(),
        );

        if (!$id) {
            $data["created_by"] = $this->login_user->id;
            $data["created_at"] = get_current_utc_time();
        }

        $data = clean_data($data);

        // basic write access: sipri_manage OR creator can edit own cards
        if ($id) {
            $existing = $this->Sipri_ideas_model->get_one_with_meta($id);
            if (!$existing || !$existing->id) {
                show_404();
            }
            if (!($this->login_user->is_admin || get_array_value($this->login_user->permissions, "sipri_manage") || $existing->created_by == $this->login_user->id)) {
                app_redirect("forbidden");
            }
        }

        $save_id = $this->Sipri_ideas_model->ci_save($data, $id);

        if ($save_id && !$id) {
            log_notification("sipri_idea_created", array("sipri_idea_id" => $save_id));
        }

        // attachments (optional, stored in timeline_file_path)
        $files_data = move_files_from_temp_dir_to_permanent_dir(get_setting("timeline_file_path"), "sipri_idea");
        if ($files_data && $files_data !== "a:0:{}") {
            $this->Sipri_idea_attachments_model->ci_save(array(
                "idea_id" => $save_id ? $save_id : $id,
                "uploaded_by" => $this->login_user->id,
                "files" => $files_data,
                "created_at" => get_current_utc_time(),
                "deleted" => 0
            ));
        }

        if ($save_id) {
            echo json_encode(array("success" => true, "message" => app_lang("record_saved"), "id" => $save_id));
        } else {
            echo json_encode(array("success" => false, "message" => app_lang("error_occurred")));
        }
    }

    public function view($id = 0) {
        validate_numeric_value($id);

        $idea = $this->Sipri_ideas_model->get_one_with_meta($id);
        if (!$idea || !$idea->id) {
            show_404();
        }

        // Client users can only access client-category ideas within their tenant
        if ($this->login_user->user_type === "client") {
            if ($idea->tenant_id != $this->login_user->sipri_tenant_id || $idea->category !== "client") {
                app_redirect("forbidden");
            }
        }

        // secret access enforcement
        if ($idea->is_secret) {
            if (!$this->has_sipri_secret_access() && !$this->Sipri_idea_access_locks_model->has_access($idea->id, $this->login_user->id)) {
                app_redirect("forbidden");
            }
        }

        $counts = $this->Sipri_idea_votes_model->get_counts($id);
        $comments = $this->Sipri_idea_comments_model->get_details(array("idea_id" => $id))->getResult();
        $attachments = $this->Sipri_idea_attachments_model->get_by_idea_id($id);
        $approvals = $this->Sipri_idea_approvals_model->get_by_idea_id($id);
        $history = $this->Sipri_idea_status_history_model->get_by_idea_id($id);

        $view_data = array(
            "idea" => $idea,
            "vote_counts" => $counts,
            "comments" => $comments,
            "attachments" => $attachments,
            "approvals" => $approvals,
            "history" => $history,
            "can_approve" => ($this->login_user->is_admin || get_array_value($this->login_user->permissions, "sipri_approve"))
        );

        return $this->template->rander("sipri/ideas/view", $view_data);
    }

    public function assign_executor() {
        $this->validate_submitted_data(array(
            "idea_id" => "required|numeric",
            "executor_user_id" => "required|numeric"
        ));

        if (!($this->login_user->is_admin || get_array_value($this->login_user->permissions, "sipri_manage") || get_array_value($this->login_user->permissions, "sipri_approve"))) {
            app_redirect("forbidden");
        }

        $idea_id = $this->request->getPost("idea_id");
        $executor_user_id = $this->request->getPost("executor_user_id");

        $idea = $this->Sipri_ideas_model->get_one_with_meta($idea_id);
        if (!$idea || !$idea->id) {
            show_404();
        }

        $from = $idea->status;
        $to = "in_progress";

        $this->Sipri_ideas_model->ci_save(array(
            "executor_user_id" => $executor_user_id,
            "status" => $to,
            "updated_at" => get_current_utc_time()
        ), $idea_id);

        $this->Sipri_idea_status_history_model->ci_save(array(
            "idea_id" => $idea_id,
            "from_status" => $from,
            "to_status" => $to,
            "changed_by" => $this->login_user->id,
            "comment" => "executor_assigned",
            "created_at" => get_current_utc_time()
        ));

        echo json_encode(array("success" => true, "message" => app_lang("record_saved")));
    }

    public function mark_done() {
        $this->validate_submitted_data(array(
            "idea_id" => "required|numeric",
            "result" => "required"
        ));

        $idea_id = $this->request->getPost("idea_id");
        $result = $this->request->getPost("result"); // done_success|done_fail
        $report = $this->request->getPost("execution_report");

        if (!in_array($result, array("done_success", "done_fail"))) {
            echo json_encode(array("success" => false, "message" => app_lang("error_occurred")));
            return;
        }

        $idea = $this->Sipri_ideas_model->get_one_with_meta($idea_id);
        if (!$idea || !$idea->id) {
            show_404();
        }

        $is_executor = ($idea->executor_user_id && (int) $idea->executor_user_id === (int) $this->login_user->id);
        if (!($this->login_user->is_admin || get_array_value($this->login_user->permissions, "sipri_manage") || $is_executor)) {
            app_redirect("forbidden");
        }

        $from = $idea->status;
        $to = $result;

        $this->Sipri_ideas_model->ci_save(array(
            "status" => $to,
            "execution_report" => $report,
            "executed_by" => $this->login_user->id,
            "executed_at" => get_current_utc_time(),
            "updated_at" => get_current_utc_time()
        ), $idea_id);

        $this->Sipri_idea_status_history_model->ci_save(array(
            "idea_id" => $idea_id,
            "from_status" => $from,
            "to_status" => $to,
            "changed_by" => $this->login_user->id,
            "comment" => $report ? $report : "",
            "created_at" => get_current_utc_time()
        ));

        echo json_encode(array("success" => true, "message" => app_lang("record_saved")));
    }

    public function archive() {
        $this->validate_submitted_data(array(
            "idea_id" => "required|numeric"
        ));

        if (!($this->login_user->is_admin || get_array_value($this->login_user->permissions, "sipri_manage"))) {
            app_redirect("forbidden");
        }

        $idea_id = $this->request->getPost("idea_id");
        $idea = $this->Sipri_ideas_model->get_one_with_meta($idea_id);
        if (!$idea || !$idea->id) {
            show_404();
        }

        $from = $idea->status;
        $to = "archived";

        $this->Sipri_ideas_model->ci_save(array(
            "status" => $to,
            "updated_at" => get_current_utc_time()
        ), $idea_id);

        $this->Sipri_idea_status_history_model->ci_save(array(
            "idea_id" => $idea_id,
            "from_status" => $from,
            "to_status" => $to,
            "changed_by" => $this->login_user->id,
            "comment" => "",
            "created_at" => get_current_utc_time()
        ));

        echo json_encode(array("success" => true, "message" => app_lang("record_saved")));
    }

    public function vote() {
        $this->validate_submitted_data(array(
            "idea_id" => "required|numeric",
            "vote" => "required"
        ));

        $idea_id = $this->request->getPost("idea_id");
        $vote = $this->request->getPost("vote");
        if (!in_array($vote, array("yes", "no", "abstain"))) {
            echo json_encode(array("success" => false, "message" => app_lang("error_occurred")));
            return;
        }

        $idea = $this->Sipri_ideas_model->get_one_with_meta($idea_id);
        if (!$idea || !$idea->id) {
            show_404();
        }

        $this->Sipri_idea_votes_model->upsert_vote($idea_id, $this->login_user->id, $vote);

        $counts = $this->Sipri_idea_votes_model->get_counts($idea_id);
        $yes = (int) $counts->yes_count;
        $no = (int) $counts->no_count;
        $den = max(1, $yes + $no);
        $rating = (int) round(($yes / $den) * 100);

        $this->Sipri_ideas_model->ci_save(array(
            "rating_percent" => $rating,
            "updated_at" => get_current_utc_time()
        ), $idea_id);

        echo json_encode(array("success" => true, "rating_percent" => $rating, "counts" => $counts));
    }

    public function add_comment() {
        $this->validate_submitted_data(array(
            "idea_id" => "required|numeric",
            "comment" => "required"
        ));

        $idea_id = $this->request->getPost("idea_id");
        $comment = $this->request->getPost("comment");

        $idea = $this->Sipri_ideas_model->get_one_with_meta($idea_id);
        if (!$idea || !$idea->id) {
            show_404();
        }

        $is_anonymous = $this->request->getPost("is_anonymous") ? 1 : 0;
        $data = array(
            "idea_id" => $idea_id,
            "user_id" => $this->login_user->id,
            "is_anonymous" => $is_anonymous,
            "comment" => $comment,
            "created_at" => get_current_utc_time(),
            "deleted" => 0
        );
        $data = clean_data($data);
        $save_id = $this->Sipri_idea_comments_model->ci_save($data);

        if ($save_id) {
            log_notification("sipri_idea_commented", array("sipri_idea_id" => $idea_id));
            echo json_encode(array("success" => true, "message" => app_lang("comment_submited")));
        } else {
            echo json_encode(array("success" => false, "message" => app_lang("error_occurred")));
        }
    }

    public function suggest() {
        $q = $this->request->getGet("q");
        $tenant_id = $this->login_user->sipri_tenant_id;
        if (!$q || !$tenant_id) {
            echo json_encode([]);
            return;
        }

        $result = $this->Sipri_ideas_model->suggest_titles($tenant_id, $q, 10);
        echo json_encode($result);
    }

    public function send_for_approval() {
        $this->require_sipri_approve();

        $this->validate_submitted_data(array(
            "idea_id" => "required|numeric",
            "approver_user_id" => "permit_empty|numeric"
        ));

        $idea_id = $this->request->getPost("idea_id");
        $approver_user_id = $this->request->getPost("approver_user_id");

        $idea = $this->Sipri_ideas_model->get_one_with_meta($idea_id);
        if (!$idea || !$idea->id) {
            show_404();
        }

        // auto-route: department manager -> tenant director (if available)
        if (!$approver_user_id) {
            $dept_model = model("App\\Models\\Sipri_departments_model");
            $tenant_model = model("App\\Models\\Sipri_tenants_model");

            $dept = $idea->department_id ? $dept_model->get_one($idea->department_id) : null;
            $tenant = $tenant_model->get_one($idea->tenant_id);

            $dept_manager = $dept && ($dept->manager_user_id || $dept->deputy_user_id) ? ($dept->manager_user_id ? $dept->manager_user_id : $dept->deputy_user_id) : null;
            $approver_user_id = $dept_manager ? $dept_manager : ($tenant && $tenant->director_user_id ? $tenant->director_user_id : null);
        }

        if (!$approver_user_id) {
            echo json_encode(array("success" => false, "message" => app_lang("error_occurred")));
            return;
        }

        // avoid duplicates
        if (!$this->Sipri_idea_approvals_model->has_approver($idea_id, $approver_user_id)) {
            // create approval entry
            $this->Sipri_idea_approvals_model->ci_save(array(
                "idea_id" => $idea_id,
                "approver_user_id" => $approver_user_id,
                "decision" => null,
                "comment" => null,
                "decided_at" => null,
                "created_at" => get_current_utc_time()
            ));
        }

        // move to on_approval
        if ($idea->status !== "on_approval") {
            $this->Sipri_ideas_model->ci_save(array(
                "status" => "on_approval",
                "updated_at" => get_current_utc_time()
            ), $idea_id);

            $this->Sipri_idea_status_history_model->ci_save(array(
                "idea_id" => $idea_id,
                "from_status" => $idea->status,
                "to_status" => "on_approval",
                "changed_by" => $this->login_user->id,
                "comment" => "",
                "created_at" => get_current_utc_time()
            ));
        }

        log_notification("sipri_idea_sent_for_approval", array("sipri_idea_id" => $idea_id));

        echo json_encode(array("success" => true, "message" => app_lang("record_saved")));
    }
}


