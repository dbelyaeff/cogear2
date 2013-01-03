<?php

/**
 * Gear
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
abstract class Gear extends Object {

    /**
     * Gear name
     * @var string
     */
    protected $name;

    /**
     * Gear description
     * @var string
     */
    protected $description;

    /**
     * Gear version
     *
     * By default it equals Cogear version
     *
     * @var string
     */
    protected $version;

    /**
     * Core version
     *
     * @var type
     */
    protected $core;

    /**
     * Package
     *
     * @var string
     */
    protected $package;

    /**
     * Gear authors name
     *
     * @var string
     */
    protected $author;

    /**
     * Gear email
     *
     * Contact email to resolve everything
     * @var string
     */
    protected $email;

    /**
     * Gear website
     *
     * @var string
     */
    protected $site;

    /**
     * Path to class file
     *
     * @var string
     */
    protected $path;

    /**
     * Directory where gear is located
     * @var string
     */
    protected $dir;

    /**
     * Relative path to folder with class
     *
     * @var string
     */
    protected $folder;

    /**
     * Order in gear stack
     *
     * Value can be positive or negative to load after or before other gears.
     *
     * @var int
     */
    protected $order = 0;

    /**
     * Class reflection
     *
     * Metaclass that stores all the info about current class
     *
     * @var ReflectionClass
     */
    protected $reflection;

    /**
     * Gear name
     *
     * How does it stored in $cogear->gears->$name
     *
     * @param   string
     */
    protected $gear;

    /**
     * Info about gear file
     *
     * @var SplFileInfo
     */
    protected $file;

    /**
     * Simple uri name
     * It can be set in configuration, but if empty — will be default gear_name
     *
     * @var string
     */
    protected $base;

    /**
     * If gear is requested by router
     * @var boolean
     */
    protected $is_requested;

    /**
     * Hooks
     *
     * @var array
     */
    protected $hooks = array();

    /**
     * Routes
     *
     * @var routes
     */
    protected $routes = array();

    /**
     * Зависимости
     *
     * @var array
     */
    protected $required;

    /**
     * От кого зависит
     *
     * @var array
     */
    protected $depends;
    /**
     * Здесь могут храниться настройки шестрёнки
     *
     * @var array
     */
    protected $options = array();

    /**
     * Конструктор
     *
     * @param   Config    $config
     */
    public function __construct($config) {
        // Принимаем настройки их конфига и сохраняем их как свойства
        parent::__construct($config, Options::SELF);
        $this->reflection = new ReflectionClass($this);
        $this->getPath();
        $this->getDir();
        $this->getFolder();
        $this->file = new SplFileInfo($this->path);
    }

    /**
     * Initialize
     */
    public function init() {
        $this->routes[$this->gear . ':maybe'] = 'index';
        $this->loadAssets();
        $this->hooks();
        $this->routes();
        event('gear.init', $this);
    }

    /**
     * Load assets
     */
    protected function loadAssets() {
        $this->assets->js->loadDir($this->dir . DS . 'js');
        $this->assets->css->loadDir($this->dir . DS . 'css');
    }

    /**
     * Set hooks
     */
    public function hooks() {
        foreach ($this->hooks as $event => $callback) {
            if (!is_array($callback)) {
                $callback = array($this, $callback);
            }
            hook($event, $callback);
        }
    }

    /**
     * Set routes
     *
     */
    public function routes() {
        foreach ($this->routes as $route => $callback) {
            if (!is_array($callback)) {
                $callback = array($this, $callback);
            }
            cogear()->router->bind($route, $callback);
        }
    }

    /**
     * Get or set gear state
     *
     * @param type $value
     * @return int
     */
    public function status($value = NULL) {
        if (Gears::ENABLED === $value) {
            $gears = cogear()->config->gears;
            if (NULL == $gears->findByValue($this->gear)) {
                $gears->append($this->gear);
                cogear()->set('gears', $gears);
            }
        } else if (Gears::DISABLED === $value) {
            $gears = cogear()->config->gears;
            if (NULL !== ($key = $gears->findByValue($this->gear))) {
                $gears->offsetUnset($key);
                cogear()->set('gears', $gears);
            }
        } else if (NULL === $value) {
            if (NULL !== cogear()->site->gears->findByValue($this->gear)) {
                return Gears::CORE;
            } elseif (NULL !== cogear()->config->gears->findByValue($this->gear)) {
                return Gears::ENABLED;
            }
            return Gears::DISABLED;
        }
    }

    /**
     * Активация шестеренки
     */
    public function enable() {
        $result = new Core_ArrayObject(array(
                    'success' => TRUE,
                    'message' => t('Шестеренка активирована!'),
                    'code' => 1,
                    'gears' => new Core_ArrayObject(),
                ));
        if ($this->status() != Gears::DISABLED) {
            $result->message = t('Шестеренка уже активирована!');
            $result->success = FALSE;
        }
        if ($this->required && FALSE === $this->required->success) {
            $gears_required = new Core_ArrayObject();
            $gears_incomp_version = new Core_ArrayObject();
            $gears_incomp = new Core_ArrayObject();
            foreach ($this->required->gear as $gear) {
                // Несовместимые шестерйнки
                if (Gears::ERROR_INCOMP === $gear->success) {
                    $gears_incomp->append($gear->name);
                }
                // Шестерёнки неправильных версий
                if (Gears::ERROR_VERSION === $gear->success) {
                    $gears_incomp_version->append($gear->name);
                }
                // Необходимые шестерёнки
                if (Gears::ERROR_REQUIRED === $gear->success) {
                    $gears_required->append($gear->name);
                }
            }
            $gears_required->count() && $result->message = t('Следующие шестерёнки должны быть активированы: ') . '<span class="label label-important">' . $gears_required->toString("</span> <span class='label label-important'>") . "</span>";
            $gears_incomp_version->count() && $result->message .= '<br/>' . t('Следующие шестеренки должны быть соответствующих версий: ') . '<span class="label label-important">' . $gears_incomp_version->toString("</span> <span class='label label-important'>") . "</span>";
            $gears_incomp->count() && $result->message .= t('Следующие шестеренки должны быть отключены: ') . '<span class="label label-important">' . $gears_incomp->toString("</span> <span class='label label-important'>") . "</span>";
            $result->success = FALSE;
        }
        $result->success && $this->status(Gears::ENABLED);
        event('gear.enable', $this, $result);
        return $result;
    }

    /**
     * Деактивация шестеренки
     */
    public function disable() {
        $result = new Core_ArrayObject(array(
                    'success' => TRUE,
                    'message' => t('Шестеренка деактивирована!'),
                ));
        if ($this->status() != Gears::ENABLED) {
            $result->message = t('Шестеренка уже деактивирована!', 'Gears');
            $result->success = FALSE;
        }
        if ($this->depends) {
            $result->success = FALSE;
            $result->message = t('Невозможно деактивировать шестеренку, потому что от неё зависят следующие шестеренки: ') . '<span class="label label-important">' . $this->depends->toString("</span> <span class='label label-important'>") . "</span>";
            //. ' <b>' . implode('</b>, <b>', array_keys($this->depends->toArray())) . '</b>';
        }
        $result->success && $this->status(Gears::DISABLED);
        event('gear.disable', $this, $result);
        return $result;
    }

    /**
     * Get Gear Path
     *
     * @return string
     */
    protected function getPath() {
        return $this->path = $this->reflection->getFileName();
    }

    /**
     * Get Gear directory
     *
     * @return  string
     */
    protected function getDir() {
        if (!$this->path)
            $this->getPath();
        return $this->dir = dirname($this->path);
    }

    /**
     * Get Gear relative folder
     *
     * @return string
     */
    protected function getFolder() {
        if (!$this->dir)
            $this->getDir();
        $this->folder = str_replace(array(ROOT, DS), array('', '/'), $this->dir);
        return self::normalizePath($this->dir);
    }

    /**
     * Normalize relative path
     *
     * For example, under windows it look like \cogear\Theme\Default\, but wee need good uri to load css, js or anything else.
     * It transorm that path to /cogear/Theme/Default/.
     * @param   string  $path
     * @return  string
     */
    public static function normalizePath($path) {
        $path = str_replace(DS, '/', $path);
        return $path;
    }

    /**
     * Prepare path
     *
     * Errors.index = Core/Errors/$dir/index
     *
     * @param	string	$path
     * @return	string  Path
     */
    public static function preparePath($path) {
        // Add extension
        strpos($path, EXT) OR $path .= EXT;
        // If it has relative path
        if (strpos($path, ROOT) === FALSE) {
            if ($path[0] == '/') {
                $path = ROOTPATH . DS . $path;
            } else {
                $path = GEARS . DS . $path;
            }
        }
        if (file_exists($path)) {
            return $path;
        }
        return NULL;
    }

    /**
     * Метод, который вызывается, когда роутер обращается к шестерёнке
     */
    public function request() {
        if (FALSE === event('gear.request', $this)->check()) {
            return;
        }
        $this->is_requested = TRUE;
    }

    /**
     * Диспатчер
     *
     * Параметры — передаваемые элементы uri
     */
    public function index() {
        if (!$args = func_get_args()) {
            $args[] = 'index';
        }
        if (!event('gear.dispatch', $this, $args)->check()) {
            return;
        }
        if (method_exists($this, $args[0] . '_action')) {
            $params = array_slice($args,1);
            event($this->gear.'.'.$args[0],$this,$params);
            $result = call_user_func_array(array($this, $args[0] . '_action'), $params);
            event($this->gear.'.'.$args[0].'.after',$this,$params);
        } elseif (method_exists($this, 'index_action')) {
            event($this->gear.'.index',$this,$args);
            call_user_func_array(array($this, 'index_action'), $args);
            event($this->gear.'.index.after',$this,$args);
        } else {
            event('404');
        }
        event('gear.dispatch.after', $this, $args);
    }

}