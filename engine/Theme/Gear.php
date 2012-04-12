<?php

/**
 * Theme gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Theme
 * @version		$Id$
 */
class Theme_Gear extends Gear {

    protected $name = 'Theme';
    protected $description = 'Manage themes';
    protected $order = -100;
    protected $hooks = array(
        'exit' => 'output',
    );
    public $current;
    public $regions;
    const SUFFIX = '_Theme';

    /**
     * Constructor
     */
    public function __construct() {
        $this->regions = new Core_ArrayObject();
        parent::__construct();
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
        //set('theme.current','Your_Theme');
        $class = self::themeToClass($theme);
        if (!class_exists($class)) {
            error(t('Theme <b>%s</b> doesn\'t exist.', 'Theme', $theme));
            $class = 'Default_Theme';
        }
        $this->current = new $class();
        $this->current->init();
        $this->current->activate();
        cogear()->gears->$theme = $this->current;
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
        $this->regions->$name OR $this->regions->$name = new Theme_Region();
        hook($name, array($this, 'showRegion'), NULL, $name);
        return event($name);
    }

    /**
     * Show region 
     * 
     * Simply echoes regions output
     * 
     * @param string $name 
     */
    public function showRegion($name) {
        $this->regions->$name === NULL && $this->regions->$name = new Theme_Region();
        echo $this->regions->$name->render();
    }
    
    /**
     * Output
     */
    public function output(){
        $this->current && $this->current->render();
    }
}

function append($name, $value) {
    $cogear = getInstance();
    $cogear->theme->regions->$name OR $cogear->theme->regions->$name = new Theme_Region();
    $cogear->theme->regions->$name->append($value);
}

function prepend($name, $value) {
    $cogear = getInstance();
    $cogear->theme->regions->$name OR $cogear->theme->regions->$name = new Theme_Region();
    $cogear->theme->regions->$name->prepend($value);
}

function inject($name, $value, $position = 0) {
    $cogear = getInstance();
    $cogear->theme->regions->$name OR $cogear->theme->regions->$name = new Theme_Region();
    $cogear->theme->regions->$name->inject($value, $position);
}

function theme($place) {
    $cogear = getInstance();
    return $cogear->theme->renderRegion($place);
}