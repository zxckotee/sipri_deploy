<?php

namespace App\Models;

class Sipri_idea_votes_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'sipri_idea_votes';
        parent::__construct($this->table);
    }

    function upsert_vote($idea_id, $user_id, $vote) {
        $table = $this->db->prefixTable('sipri_idea_votes');
        $idea_id = $this->db->escapeString($idea_id);
        $user_id = $this->db->escapeString($user_id);
        $vote = $this->db->escapeString($vote);

        $existing = $this->db->query("SELECT id FROM $table WHERE idea_id=$idea_id AND user_id=$user_id")->getRow();
        $now = get_current_utc_time();
        if ($existing && $existing->id) {
            $this->db->query("UPDATE $table SET vote='$vote', created_at='$now' WHERE id=" . $this->db->escapeString($existing->id));
            return $existing->id;
        } else {
            $this->db->query("INSERT INTO $table (idea_id,user_id,vote,created_at) VALUES ($idea_id,$user_id,'$vote','$now')");
            return $this->db->insertID();
        }
    }

    function get_counts($idea_id) {
        $table = $this->db->prefixTable('sipri_idea_votes');
        $idea_id = $this->db->escapeString($idea_id);

        $sql = "SELECT
                    SUM(CASE WHEN vote='yes' THEN 1 ELSE 0 END) AS yes_count,
                    SUM(CASE WHEN vote='no' THEN 1 ELSE 0 END) AS no_count,
                    SUM(CASE WHEN vote='abstain' THEN 1 ELSE 0 END) AS abstain_count
                FROM $table
                WHERE idea_id=$idea_id";
        $row = $this->db->query($sql)->getRow();
        if (!$row) {
            $row = (object) ["yes_count" => 0, "no_count" => 0, "abstain_count" => 0];
        }
        return $row;
    }
}


