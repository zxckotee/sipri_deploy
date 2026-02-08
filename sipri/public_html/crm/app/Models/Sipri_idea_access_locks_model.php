<?php

namespace App\Models;

class Sipri_idea_access_locks_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'sipri_idea_access_locks';
        parent::__construct($this->table);
    }

    function has_access($idea_id, $user_id) {
        $table = $this->db->prefixTable('sipri_idea_access_locks');
        $idea_id = $this->db->escapeString($idea_id);
        $user_id = $this->db->escapeString($user_id);
        $sql = "SELECT id FROM $table WHERE idea_id=$idea_id AND user_id=$user_id LIMIT 1";
        $row = $this->db->query($sql)->getRow();
        return $row && $row->id ? true : false;
    }
}


