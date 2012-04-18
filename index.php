<?php
/**
 * Cogear — simple and fast content management system.
 *
 * "Life is like development. But the last one never ends." — cogear founder Dmitriy Belyaev
 */

define('COGEAR', '2.0');
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('EXT', '.php');
define('ROOT', realpath(dirname(__FILE__)));
// For the future multisiting
define('SITE', ROOT);
define('CACHE',ROOT.DS.'cache');
define('ENGINE',ROOT.DS.'engine');
define('GEARS', ROOT.DS.'gears');
define('THEMES',ROOT.DS.'themes');
define('UPLOADS', ROOT . DS . 'uploads');
define('PHP_FILE_PREFIX', '<?php ' . "\n");
define('IGNITE', time());

//ini_set('display_errors', 1);
//ini_set('error_reporting', E_ALL);
/**
 * Search for file — layerd pancake ideology
 *
 * @param string $file
 * @return string|array
 */
function find($file) {
    $paths = array(
        ENGINE . DS . $file,
        ENGINE . DS . 'Core' . DS . $file,
        GEARS . DS . $file,
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
 * Autoload
 *
 * @param   $class  Class name.
 * @return  boolean
 */
function autoload($class) {
    $filename = str_replace('_', DS, $class);
    if ($path = find($filename.EXT)) {
        include $path;
        return TRUE;
    }
    if(function_exists('event')){
        if($result = event('autoload',$class)){
            return $result;
        }
    }
    return NULL;
}

// Register with autoload
spl_autoload_register('autoload');

$cogear = Cogear::getInstance();
$cogear->load();
event('ignite');
event('done');
event('exit');