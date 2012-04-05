<?php
/**
 * Cogear — simple and fast site management system.
 *
 * Created by Dmitriy Belyaev at the year of 2011.
 *
 * "Life is like the development. But the last one never ends." — Dmitriy Belyaev
 */
// Version
define('COGEAR', '2.0');
// Constants
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('EXT', '.php');
define('ROOT', realpath(dirname(__FILE__)));
define('GEARS_FOLDER', 'gears');
define('THEMES_FOLDER','themes');
// Core gears
define('ENGINE', ROOT . DS . 'engine');
// Gears for all sites
define('GEARS', ROOT . DS . GEARS_FOLDER);
define('THEMES', ROOT . DS . THEMES_FOLDER);
define('SITES', ROOT . DS . 'sites');
define('DEFAULT_SITE', SITES . DS . 'default');
define('PHP_FILE_PREFIX', '<?php ' . "\n");
define('IGNITE', time());
// Define error reporting level
error_reporting(E_ALL | E_STRICT);

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
    if(defined('SITE')){
        $paths[] = SITE . DS . GEARS_FOLDER . DS . $file;
        $paths[] = SITE . DS . THEMES_FOLDER . DS . $file;
    }
    $result = array();
    while ($path = array_pop($paths)) {
        if (strpos($path, '*') !== FALSE && $files = glob($path)) {
            foreach ($files as $file) {
                $result[str_replace($path, '', $file)] = $file;
            }
        } elseif (file_exists($path)) {
            return $path;
        }
    }
    return $result ? $result : FALSE;
}

/**
 * Simple debug
 *
 * @param   mixed   $data
 */
function debug() {
    echo '<pre>';
    $args = func_get_args();
    call_user_func_array('var_dump', $args);
    echo '</pre>';
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
    return FALSE;
}

// Register with autoload
spl_autoload_register('autoload');

$cogear = Cogear::getInstance();
// Some root classes are needed to be preloaded
$cogear->request = new Request();
// Set host
$host = $cogear->request->get('HTTP_HOST');
// Defince site folder
// Check if main
if (substr_count($host, '.') > 1) {
    if (!is_dir(SITES . DS . $host)) {
        list($subdomain, $host) = preg_split('#[\.]+#', $host, 2, PREG_SPLIT_NO_EMPTY);
        define('SUBDOMAIN', $subdomain);
    }
}
defined('SITE') OR is_dir(SITES . DS . $host) && define('SITE', SITES . DS . $host) OR define('SITE', DEFAULT_SITE);
define('SITE_GEARS', SITE . DS . GEARS_FOLDER);
$cogear->config = new Config(SITE . DS . 'settings' . EXT);
define('DEVELOPMENT', $cogear->config->development);
$folder = basename(ROOT);
if (!in_array($folder, array($host,'www', 'public_html', 'htdocs', SITE))) {
    define('SUBDIR', $folder);
}
if (($port = $cogear->request->get('SERVER_PORT')) != 80) {
    $host .= ':' . $port;
}
define('SITE_URL', $host);
// Define uploads folder
defined('UPLOADS') OR define('UPLOADS', SITE . DS . 'uploads');
$cogear->system_cache = new Cache(array('adapter' => 'Cache_Adapter_File', 'path' => SITE . DS . 'cache' . DS . 'system', 'enabled' => !DEVELOPMENT));
$cogear->cache = $cogear->config->cache ? new Cache($cogear->config->cache) : $cogear->system_cache;
if (!$options = $cogear->config->cookies) {
    $options = array(
        'name' => 'session',
        'cookie_domain' => '.' . SITE_URL,
    );
}
$cogear->router = new Router();
$cogear->assets = new Harvester();
$cogear->response = new Response();
$cogear->session = Session::factory('session', $options);
// Load current site settings if file exists
$cogear->config->load(SITE.DS.'config'.EXT);
// Load gears
$cogear->loadGears();
event('ignite');
event('done');
event('exit');