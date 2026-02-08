<?php
// Включаем отображение всех ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>Тест ошибок PHP</h1>";

echo "<h2>1. Проверка базовых функций PHP</h2>";
echo "PHP версия: " . PHP_VERSION . "<br>";
echo "Расширение mysqli: " . (extension_loaded('mysqli') ? '✓' : '✗') . "<br>";
echo "Расширение pdo_mysql: " . (extension_loaded('pdo_mysql') ? '✓' : '✗') . "<br>";

echo "<h2>2. Проверка путей</h2>";
$fcpath = __DIR__;
echo "FCPATH (текущая директория): $fcpath<br>";

$paths_file = $fcpath . '/app/Config/Paths.php';
echo "Файл Paths.php: " . ($paths_file) . "<br>";
echo "Существует: " . (file_exists($paths_file) ? '✓' : '✗') . "<br>";

echo "<h2>3. Попытка загрузить Paths.php</h2>";
try {
    require_once $paths_file;
    echo "✓ Paths.php загружен успешно<br>";
    
    if (class_exists('Config\Paths')) {
        $paths = new Config\Paths();
        echo "✓ Класс Paths создан<br>";
        echo "Writable directory: " . $paths->writableDirectory . "<br>";
        echo "Существует: " . (is_dir($paths->writableDirectory) ? '✓' : '✗') . "<br>";
        echo "Доступна для записи: " . (is_writable($paths->writableDirectory) ? '✓' : '✗') . "<br>";
    }
} catch (Throwable $e) {
    echo "✗ Ошибка: " . $e->getMessage() . "<br>";
    echo "Файл: " . $e->getFile() . "<br>";
    echo "Строка: " . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>4. Попытка загрузить bootstrap.php</h2>";
try {
    $system_path = $fcpath . '/system';
    $bootstrap_file = $system_path . '/bootstrap.php';
    echo "Bootstrap файл: $bootstrap_file<br>";
    echo "Существует: " . (file_exists($bootstrap_file) ? '✓' : '✗') . "<br>";
    
    if (file_exists($bootstrap_file)) {
        // Не загружаем полностью, просто проверяем
        echo "✓ Файл найден<br>";
    }
} catch (Throwable $e) {
    echo "✗ Ошибка: " . $e->getMessage() . "<br>";
}

echo "<h2>5. Проверка переменных окружения</h2>";
$env_vars = ['SIPRI_DB_HOST', 'SIPRI_DB_USER', 'SIPRI_DB_NAME', 'SIPRI_DB_PASS'];
foreach ($env_vars as $var) {
    $value = getenv($var);
    echo "$var: " . ($value ?: 'не установлено') . "<br>";
}
