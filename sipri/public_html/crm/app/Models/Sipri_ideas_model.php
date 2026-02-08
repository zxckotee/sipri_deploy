<?php

namespace App\Models;

class Sipri_ideas_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'sipri_ideas';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $ideas_table = $this->db->prefixTable('sipri_ideas');
        $users_table = $this->db->prefixTable('users');

        $where = " AND $ideas_table.deleted=0 ";

        $tenant_id = $this->_get_clean_value($options, "tenant_id");
        if ($tenant_id) {
            $where .= " AND $ideas_table.tenant_id=" . $this->db->escapeString($tenant_id);
        }

        $department_id = $this->_get_clean_value($options, "department_id");
        if ($department_id) {
            $where .= " AND $ideas_table.department_id=" . $this->db->escapeString($department_id);
        }

        $category = $this->_get_clean_value($options, "category");
        if ($category) {
            $where .= " AND $ideas_table.category='" . $this->db->escapeString($category) . "'";
        }

        $status = $this->_get_clean_value($options, "status");
        if ($status) {
            $where .= " AND $ideas_table.status='" . $this->db->escapeString($status) . "'";
        }

        $search = $this->_get_clean_value($options, "search");
        if ($search) {
            $search = $this->db->escapeLikeString($search);
            $where .= " AND ($ideas_table.title LIKE '%$search%' ESCAPE '!' OR $ideas_table.problem LIKE '%$search%' ESCAPE '!' OR $ideas_table.idea LIKE '%$search%' ESCAPE '!')";
        }

        $sql = "SELECT $ideas_table.*, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS created_by_name
                FROM $ideas_table
                LEFT JOIN $users_table ON $users_table.id=$ideas_table.created_by
                WHERE 1=1 $where
                ORDER BY $ideas_table.id DESC";

        return $this->db->query($sql);
    }

    function get_one_with_meta($id) {
        $ideas_table = $this->db->prefixTable('sipri_ideas');
        $users_table = $this->db->prefixTable('users');
        $exec = $this->db->prefixTable('users');
        $id = $this->db->escapeString($id);

        $sql = "SELECT $ideas_table.*,
                       CONCAT($users_table.first_name, ' ', $users_table.last_name) AS created_by_name,
                       CONCAT($exec.first_name, ' ', $exec.last_name) AS executor_name
                FROM $ideas_table
                LEFT JOIN $users_table ON $users_table.id=$ideas_table.created_by
                LEFT JOIN $exec ON $exec.id=$ideas_table.executor_user_id
                WHERE $ideas_table.deleted=0 AND $ideas_table.id=$id";
        return $this->db->query($sql)->getRow();
    }

    function suggest_titles($tenant_id, $q, $limit = 10) {
        $ideas_table = $this->db->prefixTable('sipri_ideas');
        $tenant_id = $this->db->escapeString($tenant_id);
        $q = $this->db->escapeLikeString($q);
        $limit = (int) $limit;
        if ($limit < 1 || $limit > 50) {
            $limit = 10;
        }

        $sql = "SELECT $ideas_table.id, $ideas_table.title
                FROM $ideas_table
                WHERE $ideas_table.deleted=0
                  AND $ideas_table.tenant_id=$tenant_id
                  AND $ideas_table.title LIKE '%$q%' ESCAPE '!'
                ORDER BY $ideas_table.id DESC
                LIMIT $limit";

        return $this->db->query($sql)->getResult();
    }
}


