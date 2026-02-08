<?php
//INSTALL FLYWEB
class DatabaseSettings
{
	var $settings;
	function getSettings()
	{
		// Database variables (prefer environment variables for docker/prod)
		// APP_SIPRI_DB_HOST, APP_SIPRI_DB_NAME, APP_SIPRI_DB_USER, APP_SIPRI_DB_PASS
		$settings['dbhost'] = getenv('APP_SIPRI_DB_HOST') ? getenv('APP_SIPRI_DB_HOST') : 'db';
		$settings['dbname'] = getenv('APP_SIPRI_DB_NAME') ? getenv('APP_SIPRI_DB_NAME') : 'app_sipri';
		$settings['dbusername'] = getenv('APP_SIPRI_DB_USER') ? getenv('APP_SIPRI_DB_USER') : 'app_sipri';
		$settings['dbpassword'] = getenv('APP_SIPRI_DB_PASS') ? getenv('APP_SIPRI_DB_PASS') : 'app_sipri';

		return $settings;
	}
} 

