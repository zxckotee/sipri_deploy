<?php

namespace App\Controllers;

class SipriApprovals extends SipriBaseController {

    protected $Sipri_idea_approvals_model;
    protected $Sipri_ideas_model;
    protected $Sipri_idea_status_history_model;
    protected $Sipri_departments_model;
    protected $Sipri_tenants_model;
    protected $Sipri_delegations_model;

    public function __construct() {
        parent::__construct();
        $this->require_sipri_approve();

        $this->Sipri_idea_approvals_model = model("App\\Models\\Sipri_idea_approvals_model");
        $this->Sipri_ideas_model = model("App\\Models\\Sipri_ideas_model");
        $this->Sipri_idea_status_history_model = model("App\\Models\\Sipri_idea_status_history_model");
        $this->Sipri_departments_model = model("App\\Models\\Sipri_departments_model");
        $this->Sipri_tenants_model = model("App\\Models\\Sipri_tenants_model");
        $this->Sipri_delegations_model = model("App\\Models\\Sipri_delegations_model");
    }

    public function index() {
        $pending = $this->Sipri_idea_approvals_model->get_pending_for_user($this->login_user->id, $this->login_user->sipri_tenant_id)->getResult();
        return $this->template->rander("sipri/approvals/index", array("pending" => $pending));
    }

    public function decide() {
        $this->validate_submitted_data(array(
            "idea_id" => "required|numeric",
            "decision" => "required"
        ));

        $idea_id = $this->request->getPost("idea_id");
        $decision = $this->request->getPost("decision");
        $comment = $this->request->getPost("comment");

        if (!in_array($decision, array("approved", "rejected"))) {
            echo json_encode(array("success" => false, "message" => app_lang("error_occurred")));
            return;
        }

        $idea = $this->Sipri_ideas_model->get_one_with_meta($idea_id);
        if (!$idea || !$idea->id) {
            show_404();
        }

        // approver can decide, or delegate of approver can decide
        $effective_approver_user_id = $this->Sipri_idea_approvals_model->find_effective_approver_user_id($idea_id, $this->login_user->id, $idea->tenant_id);
        if (!$effective_approver_user_id) {
            app_redirect("forbidden");
        }

        $this->Sipri_idea_approvals_model->decide($idea_id, $effective_approver_user_id, $decision, $comment ? $comment : "");

        // if dept manager approved, auto-add tenant director as next approver (if configured)
        if ($decision === "approved") {
            $dept = $idea->department_id ? $this->Sipri_departments_model->get_one($idea->department_id) : null;
            $tenant = $this->Sipri_tenants_model->get_one($idea->tenant_id);

            $dept_manager_id = $dept && ($dept->manager_user_id || $dept->deputy_user_id) ? (int) ($dept->manager_user_id ? $dept->manager_user_id : $dept->deputy_user_id) : 0;
            $director_id = $tenant && $tenant->director_user_id ? (int) $tenant->director_user_id : 0;

            if ($director_id && $dept_manager_id && $effective_approver_user_id === $dept_manager_id && $director_id !== $dept_manager_id) {
                if (!$this->Sipri_idea_approvals_model->has_approver($idea_id, $director_id)) {
                    $this->Sipri_idea_approvals_model->ci_save(array(
                        "idea_id" => $idea_id,
                        "approver_user_id" => $director_id,
                        "decision" => null,
                        "comment" => null,
                        "decided_at" => null,
                        "created_at" => get_current_utc_time()
                    ));
                }
            }
        }

        $new_status = $idea->status;
        if ($decision === "rejected" || $this->Sipri_idea_approvals_model->has_rejection($idea_id)) {
            $new_status = "rejected";
        } else if ($this->Sipri_idea_approvals_model->all_approved($idea_id)) {
            $new_status = "approved";
        } else {
            $new_status = "on_approval";
        }

        if ($new_status !== $idea->status) {
            $this->Sipri_ideas_model->ci_save(array(
                "status" => $new_status,
                "updated_at" => get_current_utc_time()
            ), $idea_id);

            $this->Sipri_idea_status_history_model->ci_save(array(
                "idea_id" => $idea_id,
                "from_status" => $idea->status,
                "to_status" => $new_status,
                "changed_by" => $this->login_user->id,
                "comment" => $comment ? $comment : "",
                "created_at" => get_current_utc_time()
            ));
        }

        log_notification("sipri_idea_decided", array("sipri_idea_id" => $idea_id));

        echo json_encode(array("success" => true, "message" => app_lang("record_saved")));
    }
}


