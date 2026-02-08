<?php

namespace App\Controllers;

class SipriSettings extends SipriBaseController {

    public function index() {
        return $this->template->rander("sipri/settings/index");
    }

    public function save() {
        $this->validate_submitted_data(array(
            "enable_web_notification" => "permit_empty",
            "language" => "permit_empty"
        ));

        $data = array(
            "enable_web_notification" => $this->request->getPost("enable_web_notification") ? 1 : 0,
            "language" => $this->request->getPost("language") ? $this->request->getPost("language") : $this->login_user->language
        );

        $data = clean_data($data);
        $this->Users_model->ci_save($data, $this->login_user->id);

        echo json_encode(array("success" => true, "message" => app_lang("record_saved")));
    }
}


