<?php

namespace App\Models;

class Sipri_idea_attachments_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'sipri_idea_attachments';
        parent::__construct($this->table);
    }

    function get_by_idea_id($idea_id) {
        $idea_id = $this->db->escapeString($idea_id);
        $table = $this->db->prefixTable('sipri_idea_attachments');
        $sql = "SELECT * FROM $table WHERE deleted=0 AND idea_id=$idea_id ORDER BY id DESC";
        return $this->db->query($sql)->getResult();
    }
}


