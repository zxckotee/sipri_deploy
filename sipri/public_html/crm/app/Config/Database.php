<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
	/**
	 * The directory that holds the Migrations
	 * and Seeds directories.
	 *
	 * @var string
	 */
	public $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

	/**
	 * Lets you choose which connection group to
	 * use if no other is specified.
	 *
	 * @var string
	 */
	public $defaultGroup = 'default';

	/**
	 * The default database connection.
	 *
	 * @var array
	 */
	public $default = [
		'DSN'      => '',
		'hostname' => 'localhost',
		'username' => 'cp91572_1',
		'password' => 'Lfhr1988',
		'database' => 'cp91572_1',
		'DBDriver' => 'MySQLi',
		'DBPrefix' => 'bro_',
		'pConnect' => false,
		'DBDebug'  => (ENVIRONMENT !== 'production'),
		'charset'  => 'utf8mb4',
		'DBCollat' => 'utf8mb4_unicode_ci',
		'swapPre'  => '',
		'encrypt'  => false,
		'compress' => false,
		'strictOn' => false,
		'failover' => [],
		'port'     => 3306,
	];

	/**
	 * This database connection is used when
	 * running PHPUnit database tests.
	 *
	 * @var array
	 */
	public $tests = [
		'DSN'      => '',
		'hostname' => '127.0.0.1',
		'username' => '',
		'password' => '',
		'database' => ':memory:',
		'DBDriver' => 'SQLite3',
		'DBPrefix' => 'db_',  // Needed to ensure we're working correctly with prefixes live. DO NOT REMOVE FOR CI DEVS
		'pConnect' => false,
		'DBDebug'  => (ENVIRONMENT !== 'production'),
		'charset'  => 'utf8',
		'DBCollat' => 'utf8_general_ci',
		'swapPre'  => '',
		'encrypt'  => false,
		'compress' => false,
		'strictOn' => false,
		'failover' => [],
		'port'     => 3306,
	];

	//--------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		// Ensure that we always set the database group to 'tests' if
		// we are currently running an automated test suite, so that
		// we don't overwrite live data on accident.
		if (ENVIRONMENT === 'testing')
		{
			$this->defaultGroup = 'tests';
		}

		// Override database settings from environment variables (for Docker/production)
		// This allows the same codebase to work in different environments
		if (getenv('SIPRI_DB_HOST')) {
			$this->default['hostname'] = getenv('SIPRI_DB_HOST');
		}
		if (getenv('SIPRI_DB_USER')) {
			$this->default['username'] = getenv('SIPRI_DB_USER');
		}
		if (getenv('SIPRI_DB_PASS')) {
			$this->default['password'] = getenv('SIPRI_DB_PASS');
		}
		if (getenv('SIPRI_DB_NAME')) {
			$this->default['database'] = getenv('SIPRI_DB_NAME');
		}
		// Всегда устанавливаем порт явно, чтобы избежать использования socket
		if (getenv('SIPRI_DB_PORT')) {
			$this->default['port'] = (int)getenv('SIPRI_DB_PORT');
		} else {
			$this->default['port'] = 3306;
		}
		
		// Важно: в Docker контейнере hostname = 'db' (имя сервиса), не 'localhost'
		// MySQLi автоматически использует TCP, когда hostname != 'localhost' и != '127.0.0.1'
		// Порт уже установлен выше, так что подключение должно работать через TCP
	}

	//--------------------------------------------------------------------

}
