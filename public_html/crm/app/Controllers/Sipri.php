<?php

namespace App\Controllers;

class Sipri extends SipriBaseController {

    public function index() {
        // Landing page (minimal for now)
        return $this->template->rander("sipri/index");
    }
}


