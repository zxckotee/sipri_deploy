<?php

namespace App\Controllers;

class SipriSupport extends SipriBaseController {

    public function index() {
        // Use existing ticket module as “support + history”
        if (get_setting("module_ticket")) {
            app_redirect("tickets");
        }
        return $this->template->rander("sipri/support/index");
    }
}


