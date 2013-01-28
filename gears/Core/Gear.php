<?php

/**
 * Абстрактная шестерёнка
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
abstract class Gear extends Object {

    /**
     * Имя шестерёнки
     * @var string
     */
    protected $name;

    /**
     * Описание шестерёнки
     * @var string
     */
    protected $description;

    /**
     * Версия шестерёнки
     *
     * By default it equals Cogear version
     *
     * @var string
     */
    protected $version;

    /**
     * Версия ядра системы
     *
     * @var type
     */
    protected $core;

    /**
     * Пакет шестерёнки
     *
     * @var string
     */
    protected $package;

    /**
     * Автор шестерёнки
     *
     * @var string
     */
    protected $author;

    /**
     * Электропочта автора шестерёнки
     *
     * @var string
     */
    protected $email;

    /**
     * Веб-сайт шестерёнки
     *
     * @var string
     */
    protected $site;

    /**
     * Путь к файлу класса
     *
     * @var string
     */
    protected $path;

    /**
     * Директория, в которой расположен файл класса
     *
     * @var string
     */
    protected $dir;

    /**
     * Относительный путь к папке для uri-адреса
     *
     * @var string
     */
    protected $folder;

    /**
     * Порядок загрузки в стеке шестерёнок
     *
     * @var int
     */
    protected $order = 0;

    /**
     * Класс-отражение
     *
     * Мета-класс, содержаший информацию о данном классе.
     *
     * @var ReflectionClass
     */
    protected $reflection;

    /**
     * Машинное имя шестерёнки
     *
     * Таким оно хранится в системе $cogear->gears->$name
     *
     * @param   string
     */
    protected $gear;

    /**
     * Информация о файле класса шестерёнки
     *
     * @var SplFileInfo
     */
    protected $file;

    /**
     * Флаг, указывающий на то, является ли шестерёнка запрашиваемой через роутер
     *
     * @var boolean
     */
    protected $is_requested;

    /**
     * Хуки
     *
     * @var array
     */
    protected $hooks = array();

    /**
     * Пути
     *
     * @var routes
     */
    protected $routes = array();

    /**
     * Зависимости шестерёнки
     *
     * @var array
     */
    protected $required;

    /**
     * От кого зависит шестерёнка
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
     * Инициализация
     */
    public function init() {
        $this->loadAssets();
        $this->hooks();
        $this->routes();
        event('gear.init', $this);
    }

    /**
     * Загруза активов
     */
    protected function loadAssets() {
        $this->assets->js->loadDir($this->dir . DS . 'js');
        $this->assets->css->loadDir($this->dir . DS . 'css');
    }

    /**
     * Установка хуков
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
     * Установка путей
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
     * Установка статуса
     *
     * @param mixed $value
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
            foreach ($this->required->gears as $gear) {
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
     * Получения пути к классу шестерёнки
     *
     * @return string
     */
    protected function getPath() {
        return $this->path = $this->reflection->getFileName();
    }

    /**
     * Получение папки шестерёнки
     *
     * @return  string
     */
    protected function getDir() {
        if (!$this->path)
            $this->getPath();
        return $this->dir = dirname($this->path);
    }

    /**
     * Получения относительного пути к папки с шестерёнкой
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
     * Нормализация пути
     *
     * Например, под Windows путь выглядит как \cogear\Theme\.
     * Поэтому мы превращаем его в /cogear/Theme/.
     * @param   string  $path
     * @return  string
     */
    public static function normalizePath($path) {
        $path = str_replace(DS, '/', $path);
        return $path;
    }

    /**
     * Подготовка пути
     *
     * Путь User/templates/index.php ведёт к /gears/User/templates/index.php
     *
     * Если первым поставить /, то будет вести от корня. Пример: /themes/Default/templates/index.php
     *
     * @param	string	$path
     * @return	string  Path
     */
    public static function preparePath($path) {
        // Добавление расширения
        strpos($path, EXT) OR $path .= EXT;
        // Проверка на относительность
        if (strpos($path, ROOT) === FALSE) {
            if ($path[0] == '/') {
                $path = ROOT . DS . $path;
            } else {
                $path = GEARS . DS . $path;
            }
        }
        return $path;
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

}