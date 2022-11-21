<?php
//Retrieve the environment variables needed for this application to work
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if (!defined('API_VER')) {
	define('API_VER', $_ENV['API_VER']);
}

DB::$dbName = $_ENV['DB_NAME'];
DB::$user = $_ENV['DB_USER'];
DB::$password = $_ENV['DB_PASSWORD'];
