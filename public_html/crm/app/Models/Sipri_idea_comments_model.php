<?php

namespace App\Models;

class Sipri_idea_comments_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'sipri_idea_comments';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $comments_table = $this->db->prefixTable('sipri_idea_comments');
        $users_table = $this->db->prefixTable('users');

        $where = " AND $comments_table.deleted=0 ";
        $idea_id = $this->_get_clean_value($options, "idea_id");
        if ($idea_id) {
            $where .= " AND $comments_table.idea_id=" . $this->db->escapeString($idea_id);
        }

        $sql = "SELECT $comments_table.*, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS user_name
                FROM $comments_table
                LEFT JOIN $users_table ON $users_table.id=$comments_table.user_id
                WHERE 1=1 $where
                ORDER BY $comments_table.id ASC";
        return $this->db->query($sql);
    }
}


