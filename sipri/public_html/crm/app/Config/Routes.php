<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Dashboard');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Dashboard::index');

//custom routing for custom pages
//this route will move 'about/any-text' to 'domain.com/about/index/any-text'
$routes->add('about/(:any)', 'About::index/$1');

//add routing for controllers
$excluded_controllers = array("About", "App_Controller", "Security_Controller", "SipriInstaller", "Sipri", "SipriIdeas", "SipriApprovals", "SipriContacts", "SipriDocuments", "SipriProfile");
$controller_dropdown = array();
$dir = "./app/Controllers/";
if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            $controller_name = substr($file, 0, -4);
            if ($file && $file != "." && $file != ".." && $file != "index.html" && $file != ".gitkeep" && !in_array($controller_name, $excluded_controllers)) {
                $controller_dropdown[] = $controller_name;
            }
        }
        closedir($dh);
    }
}

foreach ($controller_dropdown as $controller) {
    $routes->get(strtolower($controller), "$controller::index");
    $routes->get(strtolower($controller) . '/(:any)', "$controller::$1");
    $routes->post(strtolower($controller) . '/(:any)', "$controller::$1");
}

// SIPRI explicit routes (keep out of legacy dynamic routing)
$routes->get('sipri', 'Sipri::index');
$routes->get('sipri/ideas', 'SipriIdeas::index');
$routes->post('sipri/ideas/(:any)', 'SipriIdeas::$1');
$routes->get('sipri/ideas/(:any)', 'SipriIdeas::$1');
$routes->post('sipri/ideas', 'SipriIdeas::save');
$routes->get('sipri/ideas/suggest', 'SipriIdeas::suggest');
$routes->post('sipri/ideas/vote', 'SipriIdeas::vote');
$routes->post('sipri/ideas/add_comment', 'SipriIdeas::add_comment');
$routes->post('sipri/ideas/send_for_approval', 'SipriIdeas::send_for_approval');
$routes->get('sipri/approvals', 'SipriApprovals::index');
$routes->post('sipri/approvals/(:any)', 'SipriApprovals::$1');
$routes->post('sipri/approvals/decide', 'SipriApprovals::decide');
$routes->get('sipri/contacts', 'SipriContacts::index');
$routes->get('sipri/documents', 'SipriDocuments::index');
$routes->post('sipri/documents/get_article_suggestion', 'SipriDocuments::get_article_suggestion');
$routes->get('sipri/documents/category/(:num)', 'SipriDocuments::category/$1');
$routes->get('sipri/documents/view/(:num)', 'SipriDocuments::view/$1');
$routes->get('sipri/profile', 'SipriProfile::index');
$routes->post('sipri/profile/(:any)', 'SipriProfile::$1');
$routes->post('sipri/profile/save', 'SipriProfile::save');
$routes->get('sipri/profile/departments_json', 'SipriProfile::departments_json');
$routes->get('sipri/settings', 'SipriSettings::index');
$routes->post('sipri/settings/save', 'SipriSettings::save');
$routes->get('sipri/support', 'SipriSupport::index');
$routes->get('sipri/admin', 'SipriAdmin::index');
$routes->post('sipri/admin/save_settings', 'SipriAdmin::save_settings');
$routes->get('sipri/tenants', 'SipriTenants::index');
$routes->get('sipri/tenants/list_data', 'SipriTenants::list_data');
$routes->post('sipri/tenants/(:any)', 'SipriTenants::$1');
$routes->get('sipri/departments', 'SipriDepartments::index');
$routes->get('sipri/departments/list_data', 'SipriDepartments::list_data');
$routes->post('sipri/departments/(:any)', 'SipriDepartments::$1');
$routes->get('sipri/delegations', 'SipriDelegations::index');
$routes->get('sipri/delegations/list_data', 'SipriDelegations::list_data');
$routes->post('sipri/delegations/(:any)', 'SipriDelegations::$1');
$routes->get('sipri/installer/migrate', 'SipriInstaller::migrate');

//add uppercase links
$routes->get("Plugins", "Plugins::index");
$routes->get("Plugins/(:any)", "Plugins::$1");
$routes->post("Plugins/(:any)", "Plugins::$1");

$routes->get("Updates", "Updates::index");
$routes->get("Updates/(:any)", "Updates::$1");
$routes->post("Updates/(:any)", "Updates::$1");

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
