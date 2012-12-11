<?php

/**
 * Шестерёнка Темы
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Theme_Gear extends Gear {

    protected $hooks = array(
        'exit' => 'output',
    );
    public $regions;

    const SUFFIX = '_Theme';

    protected $defaults;

    /**
     * Конструктор
     */
    public function __construct($xml) {
        parent::__construct($xml);
        $this->regions = new Core_ArrayObject();
    }

    /**
     * Настройки по умолчанию для всех тем
     *
     * @return SimpleXMLObject
     */
    public function getDefaultSettings() {
        return $this->defaults ? $this->defaults : $this->defaults = new SimpleXMLElement(file_get_contents(GEARS . DS . 'Theme' . DS . 'default.xml'));
    }

    /**
     * Init
     */
    public function init() {
        hook('gear.request', array($this, 'handleGearRequest'));
        if ($favicon = config('theme.favicon')) {
            hook('theme.head.meta.after', array($this, 'renderFavicon'));
        }
        hook('callback.before', array($this, 'catchOutput'), NULL, 'start');
        hook('callback.after', array($this, 'catchOutput'), NULL, 'finish');
        parent::init();
    }

    /**
     * hook Menu
     *
     * @param string  $name
     * @param object $menu
     */
    public function menu($name, $menu) {
        switch ($name) {
            case 'admin':
                $menu->register(array(
                    'label' => icon('eye-open') . ' ' . t('Theme', 'Theme'),
                    'link' => l('/admin/theme'),
                    'order' => 200,
                ));
                break;
        }
    }

    /**
     * Admin dispatcher
     *
     * @param type $action
     */
    public function admin($action = 'settings') {
        $form = new Form('Theme/forms/choose');
        $form->elements->theme->setValues($this->getThemes());
        $form->elements->theme->setValue(config('theme.current'));
        if ($result = $form->result()) {
            cogear()->set('theme.current', $result->theme);
        }
        $form->show();
    }

    /**
     * Get installed themes
     *
     * @return  array
     */
    private function getThemes() {
        $files = glob(THEMES . DS . '*' . DS . 'theme.xml');
        foreach ($files as $file) {
            $xml = new SimpleXMLElement(file_get_contents($file));
            $name = $xml->attributes()->name->__toString();
            $themes[$name] = $xml;
        }
        return $themes;
    }

    /**
     * Catch output
     *
     * @param string $mode
     */
    public function catchOutput($Router, $mode) {
        switch ($mode) {
            case 'start':
            default:
                ob_start();
                break;
            case 'finish':
                append('content', ob_get_contents());
                ob_end_clean();
                break;
        }
    }

    /**
     * Handle gear request
     *
     * Set theme, initialize it.
     *
     * @param   object  $Gear
     */
    public function handleGearRequest($Gear) {
        $this->choose();
    }

    /**
     * Init current theme
     *
     * @param string $theme
     * @param boolean $final
     */
    public function choose($theme = NULL) {
        $theme OR $theme = config('theme.current', 'Default');
        $class = self::themeToClass($theme);
        if (!class_exists($class)) {
            error(t('Theme <b>%s</b> doesn\'t exist.', 'Theme', $theme));
            $class = 'Default_Theme';
            $theme = 'Default';
            $this->choose('Default');
        }
        $xml = THEMES . DS . $theme . DS . 'theme.xml';
        $config = new SimpleXMLElement(file_get_contents($xml));
        $this->object(new $class($config));
        $this->object()->init();
    }

    /**
     *
     * @param type $theme
     */
    public function set($theme) {
        cogear()->set('theme.current', $theme);
    }

    /**
     * Transform theme name to class name
     *
     * @param   string  $theme
     */
    public static function themeToClass($theme) {
        return $theme . self::SUFFIX;
    }

    /**
     * Transform class name to theme name
     *
     * @param   string  $theme
     */
    public static function classToTheme($class) {
        return substr($class, 0, strrpos($class, self::SUFFIX));
    }

    /**
     * Render favicon
     */
    public function renderFavicon() {
        echo '<link rel="shortcut icon" href="' . Url::toUri(UPLOADS) . cogear()->get('theme.favicon') . '" />' . "\n";
    }

    /**
     * Render region
     *
     * Split it with echos output for the hooks system
     *
     * @param string $name
     */
    public function renderRegion($name) {
        $this->region($name);
        hook($name, array($this, 'showRegion'), NULL, $name);
        ob_start();
        event($name);
        $content = ob_get_clean();
        return $content;
    }

    /**
     * Check region for existance and create it if it's not exits
     *
     * @param string $name
     * @return  Theme_Region
     */
    public function region($name) {
        if ($this->regions->$name) {
            return $this->regions->$name;
        } else {
            return $this->regions->$name = new Theme_Region(array('name' => $name));
        }
    }

    /**
     * Show region
     *
     * Simply echoes regions output
     *
     * @param string $name
     */
    public function showRegion($name) {
        $this->region($name);
        echo $this->regions->$name->render();
    }

    /**
     * Output
     */
    public function output() {
        $this->object && $this->object()->render();
    }

}

function append($name, $value) {
    $cogear = getInstance();
    $cogear->theme->region($name)->append($value);
}

function prepend($name, $value) {
    $cogear = getInstance();
    $cogear->theme->region($name)->prepend($value);
}

function inject($name, $value, $position = 0) {
    $cogear = getInstance();
    $cogear->theme->region($name)->inject($value, $position);
}

function theme($place) {
    $cogear = getInstance();
    return $cogear->theme->renderRegion($place);
}