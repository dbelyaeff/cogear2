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
    protected $is_core = TRUE;
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
     * hook Menu
     *
     * @param string  $name
     * @param object $menu
     */
    public function menu($name,$menu){
        switch($name){
            case 'admin':
                $menu->register(array(
                    'label' => icon('eye-open').' '.t('Theme','Theme'),
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
    public function admin($action = 'settings'){
        $form = new Form('Theme/forms/choose');
        $form->elements->theme->setValues($this->getThemes());
        $form->elements->theme->setValue(config('theme.current'));
        if($result = $form->result()){
            cogear()->set('theme.current',$result->theme);
        }
        $form->show();
    }
    /**
     * Get installed themes
     *
     * @return  array
     */
    private function getThemes(){
        $scan = glob(THEMES.DS.'*'.DS.'Theme'.EXT);
        $themes = array();
        foreach($scan as $file){
            $theme_name = basename(dirname($file));
            $class = Gears::pathToGear($file).'_Theme';
            $theme = new $class;
            if($theme instanceof Theme_Object){
                $themes[$theme_name] = t($theme->name,'Themes');
            }
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
        //set('theme.current','Your_Theme');
        $class = self::themeToClass($theme);
        if (!class_exists($class)) {
            error(t('Theme <b>%s</b> doesn\'t exist.', 'Theme', $theme));
            $class = 'Default_Theme';
        }
        $this->object(new $class());
        $this->object()->init();
        $this->object()->enable();
        cogear()->gears->$theme = $this->object();
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