<?php

namespace App\Models;

class Sipri_departments_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'sipri_departments';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $table = $this->db->prefixTable('sipri_departments');
        $tenants = $this->db->prefixTable('sipri_tenants');
        $users = $this->db->prefixTable('users');
        $where = " AND $table.deleted=0 ";

        $tenant_id = $this->_get_clean_value($options, "tenant_id");
        if ($tenant_id) {
            $where .= " AND $table.tenant_id=" . $this->db->escapeString($tenant_id);
        }

        $sql = "SELECT $table.*,
                       $tenants.name AS tenant_name,
                       CONCAT($users.first_name,' ',$users.last_name) AS manager_name
                FROM $table
                LEFT JOIN $tenants ON $tenants.id=$table.tenant_id
                LEFT JOIN $users ON $users.id=$table.manager_user_id
                WHERE 1=1 $where
                ORDER BY $table.name ASC";
        return $this->db->query($sql);
    }
}


