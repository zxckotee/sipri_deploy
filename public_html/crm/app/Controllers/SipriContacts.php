<?php

namespace App\Controllers;

class SipriContacts extends SipriBaseController {

    public function index() {
        $db = db_connect();
        $users_table = $db->prefixTable("users");
        $clients_table = $db->prefixTable("clients");
        $departments_table = $db->prefixTable("sipri_departments");

        $tenant_id = $this->login_user->sipri_tenant_id;
        $tab = clean_data($this->request->getGet("tab")) ?: "colleagues";

        $colleagues = [];
        $client_contacts = [];
        $partner_tenants = [];

        if ($tenant_id) {
            $tid = $db->escapeString($tenant_id);
            $colleagues = $db->query("SELECT id, first_name, last_name, image, sipri_position, sipri_department_id
                                      FROM $users_table
                                      WHERE deleted=0 AND status='active' AND user_type='staff' AND sipri_tenant_id=$tid
                                      ORDER BY first_name ASC")->getResult();

            $client_contacts = $db->query("SELECT $users_table.id, $users_table.first_name, $users_table.last_name, $users_table.image,
                                                  $clients_table.company_name
                                           FROM $users_table
                                           LEFT JOIN $clients_table ON $clients_table.id=$users_table.client_id AND $clients_table.deleted=0
                                           WHERE $users_table.deleted=0 AND $users_table.status='active' AND $users_table.user_type='client' AND $users_table.sipri_tenant_id=$tid
                                           ORDER BY $clients_table.company_name ASC, $users_table.first_name ASC")->getResult();
        }

        // Smеzhnie predpriyatiya: show only enterprises list, no personal data
        if ($db->tableExists("sipri_tenants")) {
            $tenants_table = $db->prefixTable("sipri_tenants");
            $where = "WHERE deleted=0 AND is_active=1";
            if ($tenant_id) {
                $tid = $db->escapeString($tenant_id);
                $where .= " AND id<>$tid";
            }

            // corp visibility: show only tenants within same corporation key (if set)
            $corp_key = null;
            if ($tenant_id) {
                $corp_row = $db->query("SELECT corp_key FROM $tenants_table WHERE id=" . $db->escapeString($tenant_id) . " LIMIT 1")->getRow();
                $corp_key = $corp_row ? $corp_row->corp_key : null;
                if ($corp_key) {
                    $where .= " AND corp_key='" . $db->escapeString($corp_key) . "'";
                }
            }

            $partner_tenants = $db->query("SELECT id, name, slug, corp_key FROM $tenants_table $where ORDER BY name ASC")->getResult();

            // attach department structure without personal data
            if ($db->tableExists("sipri_departments") && count($partner_tenants)) {
                $tenant_ids = array_map(function ($t) { return (int) $t->id; }, $partner_tenants);
                $in = implode(",", $tenant_ids);
                $deps = $db->query("SELECT id, tenant_id, parent_id, name
                                    FROM $departments_table
                                    WHERE deleted=0 AND is_active=1 AND tenant_id IN ($in)
                                    ORDER BY tenant_id ASC, name ASC")->getResult();
                $map = [];
                foreach ($deps as $d) {
                    $map[(int) $d->tenant_id][] = $d;
                }
                foreach ($partner_tenants as $t) {
                    $t->departments = isset($map[(int) $t->id]) ? $map[(int) $t->id] : [];
                }
            }
        }

        return $this->template->rander("sipri/contacts/index", [
            "tab" => $tab,
            "colleagues" => $colleagues,
            "client_contacts" => $client_contacts,
            "partner_tenants" => $partner_tenants
        ]);
    }
}


