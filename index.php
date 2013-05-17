<?php

/**
 * Когир — быстрый и легкий фреймфорк, написанный на языке веб-програмимрования PHP
 *
 * "Жизнь — как разработка. Только в отличие от первой последняя никогда не заканчивается." — основатель проекта, Беляев Дмитрий
 */
define('IGNITE', microtime(TRUE));
define('START_MEMORY', memory_get_usage());
/**
 * Маленький и быстрый бенчмарк. Утилита для тестирования производительности
 *
 * @staticvar array $points
 * @param string|NULL $point
 */
function bench($point = NULL) {
    static $points = array();
    if (!$point) {
        return $points;
    }
    if (strpos($point, '.end')) {
        $point = substr($point, 0, strpos($point, '.end'));
        if (isset($points[$point . '.start'])) {
            $start_time = $points[$point . '.start']['time'];
            $start_memory = $points[$point . '.start']['memory'];
            unset($points[$point . '.start']);
        }
    } elseif (strpos($point, '.start')) {
        $start_time = 0;
        $start_memory = 0;
    }
    isset($start_time) OR $start_time = IGNITE;
    isset($start_memory) OR $start_memory = START_MEMORY;

    isset($points[$point]) OR $points[$point] = array(
        'time' => microtime(TRUE) - $start_time,
        'memory' => memory_get_usage() - $start_memory,
    );
}

bench('ignite');

define('COGEAR', '2.2');
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('EXT', '.php');
define('ROOT', realpath(dirname(__FILE__)));
// Для мультисайтинга — на будущее
define('SITE', ROOT);
define('CACHE', ROOT . DS . 'cache');
define('GEARS', ROOT . DS . 'gears');
define('THEMES', ROOT . DS . 'themes');
define('TEMP', ROOT.DS.'temp');
define('LANG', 'lang');
define('UPLOADS', ROOT . DS . 'uploads');
define('PHP_FILE_PREFIX', '<?php ' . "\n");

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
/**
 * Поиск файлов
 *
 * @param string $file
 * @return string|array
 */
function find($file) {
    $paths = array(
        GEARS . DS . $file,
        GEARS . DS . 'Core' . DS . $file,
        THEMES . DS . $file,
    );
    while ($path = array_pop($paths)) {
        if (file_exists($path)) {
            return $path;
        }
    }
    return FALSE;
}

/**
 * Автозагрузка
 *
 * @param   $class  Class name.
 * @return  boolean
 */
function autoload($class) {
    $filename = str_replace('_', DS, $class);
    if ($path = find($filename . EXT)) {
        include $path;
        return TRUE;
    }
    return NULL;
}

/**
 * Маленький и быстрый дебаггер
 *
 * @param mixed     $data
 * @param boolean   $type Выводить var_export или var_dump
 */
function debug($data, $type = FALSE) {
    echo '<pre class="well">';
    $type ? var_export($data) : var_dump($data);
    echo '</pre>';
}


// Регистрация автозагрузчика
spl_autoload_register('autoload');

$cogear = Cogear::getInstance();
$cogear->load();
event('ignite');
event('done');
event('exit');

