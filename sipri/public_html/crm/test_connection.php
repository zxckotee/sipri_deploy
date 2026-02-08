<?php
// Тест подключения к БД из PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Тест подключения к базе данных</h1>";

$host = getenv('SIPRI_DB_HOST') ?: 'db';
$user = getenv('SIPRI_DB_USER') ?: 'sipri';
$pass = getenv('SIPRI_DB_PASS') ?: 'sipri';
$dbname = getenv('SIPRI_DB_NAME') ?: 'sipri';
$port = 3306;

echo "<h2>Параметры подключения:</h2>";
echo "<ul>";
echo "<li>Host: $host</li>";
echo "<li>User: $user</li>";
echo "<li>Database: $dbname</li>";
echo "<li>Port: $port</li>";
echo "</ul>";

echo "<h2>Попытка подключения:</h2>";

try {
    $conn = new mysqli($host, $user, $pass, $dbname, $port);
    
    if ($conn->connect_error) {
        echo "<p style='color: red;'>✗ Ошибка подключения: " . $conn->connect_error . "</p>";
    } else {
        echo "<p style='color: green;'>✓ Подключение успешно!</p>";
        
        // Проверяем таблицы
        $result = $conn->query("SHOW TABLES");
        if ($result) {
            $tables = $result->fetch_all();
            echo "<p>Найдено таблиц: " . count($tables) . "</p>";
            
            // Проверяем таблицу bro_users
            if ($conn->query("SELECT COUNT(*) as cnt FROM bro_users") !== false) {
                $row = $conn->query("SELECT COUNT(*) as cnt FROM bro_users")->fetch_assoc();
                echo "<p style='color: green;'>✓ Таблица bro_users доступна, записей: " . $row['cnt'] . "</p>";
            } else {
                echo "<p style='color: orange;'>⚠ Таблица bro_users не найдена или недоступна</p>";
            }
        }
        
        $conn->close();
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Исключение: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>Переменные окружения:</h2>";
echo "<pre>";
echo "SIPRI_DB_HOST: " . (getenv('SIPRI_DB_HOST') ?: 'не установлено') . "\n";
echo "SIPRI_DB_USER: " . (getenv('SIPRI_DB_USER') ?: 'не установлено') . "\n";
echo "SIPRI_DB_NAME: " . (getenv('SIPRI_DB_NAME') ?: 'не установлено') . "\n";
echo "SIPRI_DB_PASS: " . (getenv('SIPRI_DB_PASS') ? '***' : 'не установлено') . "\n";
echo "</pre>";
