<?php

namespace App\Models;

class Sipri_idea_status_history_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'sipri_idea_status_history';
        parent::__construct($this->table);
    }

    function get_by_idea_id($idea_id) {
        $table = $this->db->prefixTable('sipri_idea_status_history');
        $users = $this->db->prefixTable('users');
        $idea_id = $this->db->escapeString($idea_id);

        $sql = "SELECT $table.*, CONCAT($users.first_name, ' ', $users.last_name) AS changed_by_name
                FROM $table
                LEFT JOIN $users ON $users.id=$table.changed_by
                WHERE $table.idea_id=$idea_id
                ORDER BY $table.id ASC";
        return $this->db->query($sql)->getResult();
    }
}


