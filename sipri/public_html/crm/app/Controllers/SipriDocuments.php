<?php

namespace App\Controllers;

class SipriDocuments extends SipriBaseController {

    public function index() {
        $this->check_module_availability("module_knowledge_base");
        if (!$this->login_user->sipri_tenant_id) {
            app_redirect("sipri/profile");
        }

        $type = "sipri_knowledge_base";
        $Help_categories_model = model('App\Models\Help_categories_model');

        $view_data["categories"] = $Help_categories_model->get_details(array(
            "type" => $type,
            "only_active_categories" => true,
            "sipri_tenant_id" => $this->login_user->sipri_tenant_id,
            "sipri_department_id" => $this->login_user->sipri_department_id
        ))->getResult();
        $view_data["type"] = $type;

        return $this->template->rander("help_and_knowledge_base/index", $view_data);
    }

    public function category($id) {
        validate_numeric_value($id);
        if (!$this->login_user->sipri_tenant_id) {
            app_redirect("sipri/profile");
        }

        $Help_categories_model = model('App\Models\Help_categories_model');
        $Help_articles_model = model('App\Models\Help_articles_model');

        $category_info = $Help_categories_model->get_one($id);
        if (!$category_info || !$category_info->id || $category_info->type !== "sipri_knowledge_base") {
            show_404();
        }

        // ACL: must match tenant, and department if set
        if ((int) $category_info->sipri_tenant_id !== (int) $this->login_user->sipri_tenant_id) {
            app_redirect("forbidden");
        }
        if ($category_info->sipri_department_id && (int) $category_info->sipri_department_id !== (int) $this->login_user->sipri_department_id) {
            app_redirect("forbidden");
        }

        $view_data['page_type'] = "articles_list_view";
        $view_data['type'] = $category_info->type;
        $view_data['selected_category_id'] = $category_info->id;
        $view_data['categories'] = $Help_categories_model->get_details(array(
            "type" => $category_info->type,
            "sipri_tenant_id" => $this->login_user->sipri_tenant_id,
            "sipri_department_id" => $this->login_user->sipri_department_id
        ))->getResult();

        $view_data["articles"] = $Help_articles_model->get_articles_of_a_category($id)->getResult();
        $view_data["category_info"] = $category_info;

        return $this->template->rander("help_and_knowledge_base/articles/view_page", $view_data);
    }

    public function view($id = 0) {
        validate_numeric_value($id);
        if (!$this->login_user->sipri_tenant_id) {
            app_redirect("sipri/profile");
        }

        $Help_categories_model = model('App\Models\Help_categories_model');
        $Help_articles_model = model('App\Models\Help_articles_model');

        $model_info = $Help_articles_model->get_details(array("id" => $id, "login_user_id" => $this->login_user->id))->getRow();
        if (!$model_info || $model_info->type !== "sipri_knowledge_base") {
            show_404();
        }

        $category_info = $Help_categories_model->get_one($model_info->category_id);
        if (!$category_info || !$category_info->id) {
            show_404();
        }

        // ACL: must match tenant, and department if set
        if ((int) $category_info->sipri_tenant_id !== (int) $this->login_user->sipri_tenant_id) {
            app_redirect("forbidden");
        }
        if ($category_info->sipri_department_id && (int) $category_info->sipri_department_id !== (int) $this->login_user->sipri_department_id) {
            app_redirect("forbidden");
        }

        $Help_articles_model->increas_page_view($id);

        $view_data['selected_category_id'] = $model_info->category_id;
        $view_data['type'] = $model_info->type;
        $view_data['categories'] = $Help_categories_model->get_details(array(
            "type" => $model_info->type,
            "sipri_tenant_id" => $this->login_user->sipri_tenant_id,
            "sipri_department_id" => $this->login_user->sipri_department_id
        ))->getResult();
        $view_data['page_type'] = "article_view";
        $view_data['article_info'] = $model_info;
        $view_data["scroll_to_content"] = true;

        return $this->template->rander('help_and_knowledge_base/articles/view_page', $view_data);
    }

    public function get_article_suggestion() {
        $search = $this->request->getPost("search");
        if ($search && $this->login_user->sipri_tenant_id) {
            $Help_articles_model = model('App\Models\Help_articles_model');
            $result = $Help_articles_model->get_suggestions("sipri_knowledge_base", $search);
            echo json_encode($result);
        }
    }
}


