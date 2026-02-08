<?php

namespace App\Models;

class Sipri_delegations_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'sipri_delegations';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $delegations = $this->db->prefixTable('sipri_delegations');
        $tenants = $this->db->prefixTable('sipri_tenants');
        $users = $this->db->prefixTable('users');

        $where = " AND $delegations.deleted=0 ";

        $tenant_id = $this->_get_clean_value($options, "tenant_id");
        if ($tenant_id) {
            $where .= " AND $delegations.tenant_id=" . $this->db->escapeString($tenant_id);
        }

        $to_user_id = $this->_get_clean_value($options, "to_user_id");
        if ($to_user_id) {
            $where .= " AND $delegations.to_user_id=" . $this->db->escapeString($to_user_id);
        }

        $active_only = $this->_get_clean_value($options, "active_only");
        if ($active_only) {
            $now = get_current_utc_time();
            $where .= " AND $delegations.is_active=1 AND ($delegations.starts_at IS NULL OR $delegations.starts_at<='$now') AND ($delegations.ends_at IS NULL OR $delegations.ends_at>='$now')";
        }

        $sql = "SELECT $delegations.*,
                       $tenants.name AS tenant_name,
                       CONCAT(u_from.first_name,' ',u_from.last_name) AS from_user_name,
                       CONCAT(u_to.first_name,' ',u_to.last_name) AS to_user_name
                FROM $delegations
                LEFT JOIN $tenants ON $tenants.id=$delegations.tenant_id
                LEFT JOIN $users AS u_from ON u_from.id=$delegations.from_user_id
                LEFT JOIN $users AS u_to ON u_to.id=$delegations.to_user_id
                WHERE 1=1 $where
                ORDER BY $delegations.id DESC";

        return $this->db->query($sql);
    }

    function get_active_from_user_ids($to_user_id, $tenant_id = 0) {
        $delegations = $this->db->prefixTable('sipri_delegations');
        $to_user_id = $this->db->escapeString($to_user_id);

        $where = " AND $delegations.deleted=0 AND $delegations.is_active=1 AND $delegations.to_user_id=$to_user_id";
        if ($tenant_id) {
            $tenant_id = $this->db->escapeString($tenant_id);
            $where .= " AND $delegations.tenant_id=$tenant_id";
        }

        $now = get_current_utc_time();
        $where .= " AND ($delegations.starts_at IS NULL OR $delegations.starts_at<='$now') AND ($delegations.ends_at IS NULL OR $delegations.ends_at>='$now')";

        $rows = $this->db->query("SELECT from_user_id FROM $delegations WHERE 1=1 $where")->getResult();
        $ids = [];
        foreach ($rows as $r) {
            $ids[] = (int) $r->from_user_id;
        }
        return $ids;
    }
}


