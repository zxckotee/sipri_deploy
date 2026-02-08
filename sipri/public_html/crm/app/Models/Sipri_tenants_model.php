<?php

namespace App\Models;

class Sipri_tenants_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'sipri_tenants';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $tenants = $this->db->prefixTable('sipri_tenants');
        $users = $this->db->prefixTable('users');

        $where = " AND $tenants.deleted=0 ";

        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $tenants.id=" . $this->db->escapeString($id);
        }

        $only_active = $this->_get_clean_value($options, "only_active");
        if ($only_active) {
            $where .= " AND $tenants.is_active=1";
        }

        $sql = "SELECT $tenants.*,
                       CONCAT($users.first_name,' ',$users.last_name) AS director_name
                FROM $tenants
                LEFT JOIN $users ON $users.id=$tenants.director_user_id
                WHERE 1=1 $where
                ORDER BY $tenants.name ASC";

        return $this->db->query($sql);
    }
}


