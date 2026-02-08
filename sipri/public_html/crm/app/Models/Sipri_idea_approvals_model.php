<?php

namespace App\Models;

class Sipri_idea_approvals_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'sipri_idea_approvals';
        parent::__construct($this->table);
    }

    function get_pending_for_user($user_id, $tenant_id = 0) {
        $approvals = $this->db->prefixTable('sipri_idea_approvals');
        $ideas = $this->db->prefixTable('sipri_ideas');
        $users = $this->db->prefixTable('users');
        $delegations = $this->db->prefixTable('sipri_delegations');

        $user_id = $this->db->escapeString($user_id);
        $now = get_current_utc_time();
        $where = " AND (
                        $approvals.approver_user_id=$user_id
                        OR $approvals.approver_user_id IN (
                            SELECT $delegations.from_user_id
                            FROM $delegations
                            WHERE $delegations.deleted=0
                              AND $delegations.is_active=1
                              AND $delegations.to_user_id=$user_id
                              AND ($delegations.starts_at IS NULL OR $delegations.starts_at<='$now')
                              AND ($delegations.ends_at IS NULL OR $delegations.ends_at>='$now')
                        )
                    )
                    AND ($approvals.decision IS NULL OR $approvals.decision='') ";

        if ($tenant_id) {
            $tenant_id = $this->db->escapeString($tenant_id);
            $where .= " AND $ideas.tenant_id=$tenant_id ";
        }

        $sql = "SELECT $approvals.*, $ideas.title, $ideas.status, $ideas.rating_percent,
                       CONCAT($users.first_name, ' ', $users.last_name) AS author_name
                FROM $approvals
                LEFT JOIN $ideas ON $ideas.id=$approvals.idea_id
                LEFT JOIN $users ON $users.id=$ideas.created_by
                WHERE $ideas.deleted=0 $where
                ORDER BY $approvals.id DESC";
        return $this->db->query($sql);
    }

    function has_approver($idea_id, $approver_user_id) {
        $table = $this->db->prefixTable('sipri_idea_approvals');
        $idea_id = $this->db->escapeString($idea_id);
        $approver_user_id = $this->db->escapeString($approver_user_id);
        $row = $this->db->query("SELECT id FROM $table WHERE idea_id=$idea_id AND approver_user_id=$approver_user_id LIMIT 1")->getRow();
        return $row && $row->id ? true : false;
    }

    function find_effective_approver_user_id($idea_id, $actor_user_id, $tenant_id = 0) {
        // returns the approver_user_id that should be updated (actor OR actor is a delegate of approver)
        $approvals = $this->db->prefixTable('sipri_idea_approvals');
        $delegations = $this->db->prefixTable('sipri_delegations');

        $idea_id = $this->db->escapeString($idea_id);
        $actor_user_id = $this->db->escapeString($actor_user_id);
        $now = get_current_utc_time();

        $tenant_where = "";
        if ($tenant_id) {
            $tenant_id = $this->db->escapeString($tenant_id);
            $tenant_where = " AND $delegations.tenant_id=$tenant_id";
        }

        $sql = "SELECT $approvals.approver_user_id
                FROM $approvals
                WHERE $approvals.idea_id=$idea_id
                  AND ($approvals.decision IS NULL OR $approvals.decision='')
                  AND (
                        $approvals.approver_user_id=$actor_user_id
                        OR $approvals.approver_user_id IN (
                            SELECT $delegations.from_user_id
                            FROM $delegations
                            WHERE $delegations.deleted=0 AND $delegations.is_active=1
                              AND $delegations.to_user_id=$actor_user_id
                              AND ($delegations.starts_at IS NULL OR $delegations.starts_at<='$now')
                              AND ($delegations.ends_at IS NULL OR $delegations.ends_at>='$now')
                              $tenant_where
                        )
                  )
                ORDER BY $approvals.id ASC
                LIMIT 1";

        $row = $this->db->query($sql)->getRow();
        return $row && $row->approver_user_id ? (int) $row->approver_user_id : 0;
    }

    function get_by_idea_id($idea_id) {
        $approvals = $this->db->prefixTable('sipri_idea_approvals');
        $users = $this->db->prefixTable('users');
        $idea_id = $this->db->escapeString($idea_id);

        $sql = "SELECT $approvals.*, CONCAT($users.first_name, ' ', $users.last_name) AS approver_name
                FROM $approvals
                LEFT JOIN $users ON $users.id=$approvals.approver_user_id
                WHERE $approvals.idea_id=$idea_id
                ORDER BY $approvals.id ASC";
        return $this->db->query($sql)->getResult();
    }

    function decide($idea_id, $approver_user_id, $decision, $comment = "") {
        $table = $this->db->prefixTable('sipri_idea_approvals');
        $idea_id = $this->db->escapeString($idea_id);
        $approver_user_id = $this->db->escapeString($approver_user_id);
        $decision = $this->db->escapeString($decision);
        $comment = $this->db->escapeString($comment);
        $now = get_current_utc_time();

        $sql = "UPDATE $table
                SET decision='$decision', comment='$comment', decided_at='$now'
                WHERE idea_id=$idea_id AND approver_user_id=$approver_user_id";
        return $this->db->query($sql);
    }

    function has_rejection($idea_id) {
        $table = $this->db->prefixTable('sipri_idea_approvals');
        $idea_id = $this->db->escapeString($idea_id);
        $row = $this->db->query("SELECT id FROM $table WHERE idea_id=$idea_id AND decision='rejected' LIMIT 1")->getRow();
        return $row && $row->id ? true : false;
    }

    function all_approved($idea_id) {
        $table = $this->db->prefixTable('sipri_idea_approvals');
        $idea_id = $this->db->escapeString($idea_id);
        $row = $this->db->query("SELECT COUNT(*) AS total, SUM(CASE WHEN decision='approved' THEN 1 ELSE 0 END) AS approved_count FROM $table WHERE idea_id=$idea_id")->getRow();
        if (!$row) {
            return false;
        }
        return ((int) $row->total > 0) && ((int) $row->total === (int) $row->approved_count);
    }
}


