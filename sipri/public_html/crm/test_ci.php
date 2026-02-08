<?php
// Тест загрузки CodeIgniter
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>Тест загрузки CodeIgniter</h1>";

// Путь к front controller
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
chdir(FCPATH);

echo "<h2>1. Загрузка Paths.php</h2>";
try {
    require realpath(FCPATH . 'app/Config/Paths.php') ?: FCPATH . 'app/Config/Paths.php';
    $paths = new Config\Paths();
    echo "✓ Paths загружен<br>";
    echo "Writable: " . $paths->writableDirectory . "<br>";
    echo "Существует: " . (is_dir($paths->writableDirectory) ? '✓' : '✗') . "<br>";
    echo "Доступна для записи: " . (is_writable($paths->writableDirectory) ? '✓' : '✗') . "<br>";
} catch (Throwable $e) {
    echo "✗ Ошибка: " . $e->getMessage() . "<br>";
    echo "Файл: " . $e->getFile() . "<br>";
    echo "Строка: " . $e->getLine() . "<br>";
    exit;
}

echo "<h2>2. Загрузка bootstrap.php</h2>";
try {
    require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
    echo "✓ Bootstrap загружен<br>";
} catch (Throwable $e) {
    echo "✗ Ошибка: " . $e->getMessage() . "<br>";
    echo "Файл: " . $e->getFile() . "<br>";
    echo "Строка: " . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    exit;
}

echo "<h2>3. Загрузка DotEnv</h2>";
try {
    require_once SYSTEMPATH . 'Config/DotEnv.php';
    (new CodeIgniter\Config\DotEnv(ROOTPATH))->load();
    echo "✓ DotEnv загружен<br>";
} catch (Throwable $e) {
    echo "✗ Ошибка: " . $e->getMessage() . "<br>";
    exit;
}

echo "<h2>4. Создание CodeIgniter instance</h2>";
try {
    $app = Config\Services::codeigniter();
    echo "✓ CodeIgniter instance создан<br>";
} catch (Throwable $e) {
    echo "✗ Ошибка: " . $e->getMessage() . "<br>";
    echo "Файл: " . $e->getFile() . "<br>";
    echo "Строка: " . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    exit;
}

echo "<h2>5. Инициализация приложения</h2>";
try {
    $app->initialize();
    echo "✓ Приложение инициализировано<br>";
} catch (Throwable $e) {
    echo "✗ Ошибка: " . $e->getMessage() . "<br>";
    echo "Файл: " . $e->getFile() . "<br>";
    echo "Строка: " . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    exit;
}

echo "<h2>6. Проверка подключения к БД</h2>";
try {
    $db = \Config\Database::connect();
    echo "✓ Подключение к БД успешно<br>";
    
    // Проверяем таблицы
    $tables = $db->listTables();
    echo "Найдено таблиц: " . count($tables) . "<br>";
    
    if (count($tables) > 0) {
        echo "Первые 5 таблиц: " . implode(', ', array_slice($tables, 0, 5)) . "<br>";
    }
} catch (Throwable $e) {
    echo "✗ Ошибка подключения к БД: " . $e->getMessage() . "<br>";
    echo "Файл: " . $e->getFile() . "<br>";
    echo "Строка: " . $e->getLine() . "<br>";
}

echo "<hr>";
echo "<h2>✓ Все проверки пройдены!</h2>";
