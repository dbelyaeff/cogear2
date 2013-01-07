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

    /**
     * Хуки
     *
     * @var array
     */
    protected $hooks = array(
        'logo' => 'hookLogo',
        'head' => 'hookFavicon',
        'gear.request' => 'hookGearRequest',
        'exit' => 'output',
        'menu' => 'hookMenu',
    );
    protected $routes = array(
        'admin/theme' => 'admin_action',
        'admin/theme/activate/(\w+)' => 'activate_action',
    );
    protected $access = array(
        'admin' => array(1),
        'activate' => array(1),
    );

    /**
     * Регионы в теме для вывода информации
     *
     * @var array
     */
    public $regions;

    const SUFFIX = '_Theme';

    /**
     * Выводит логотип сайта
     */
    public function hookLogo() {
        $theme = $this->object();
        if ($theme->logo) {
            echo "<a href=" . l() . "><img src=\"" . l($theme->folder) . '/' . $theme->logo . "\"></a>";
        }
    }

    /**
     * Добавляем иконку сайта в шапку
     */
    public function hookFavicon() {
        $theme = $this->object();
        $favicon = $theme->favicon ? l($theme->folder) . '/' . $theme->favicon : l('/favicon.ico');
        echo '<link rel="shortcut icon" href="' . $favicon . '">';
    }

    /**
     * Хук, который загружает тему, когда Роутер обращается к шестерёнке
     *
     * @param   object  $Gear
     */
    public function hookGearRequest($Gear) {
        if (access('Theme.admin') && $theme = $this->input->get('theme')) {
            return $this->choose($theme);
        }
        if ($Gear->options->theme) {
            $this->choose($Gear->options->theme);
        } else {
            $this->choose(config('theme.current', 'Default'));
        }
    }

    /**
     * Конструктор
     */
    public function __construct($config) {
        parent::__construct($config);
        $this->regions = new Core_ArrayObject();
    }

    /**
     * Init
     */
    public function init() {
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
    public function hookMenu($name, $menu) {
        switch ($name) {
            case 'admin':
                $menu->register(array(
                    'label' => icon('eye-open') . ' ' . t('Внешний вид'),
                    'link' => l('/admin/theme'),
                    'order' => 200,
                ));
                break;
        }
    }

    /**
     * Диспатчер админки
     *
     * @param type $action
     */
    public function admin_action() {
        new Menu_Tabs(array(
            'name' => 'admin.theme',
            'elements' => array(
                array(
                    'label' => t('Темы'),
                    'link' => l('/admin/theme')
                )
            )
        ));
        $themes = $this->getThemes();
        template('Theme/templates/list', array('themes' => $themes))->show();
    }

    /**
     * Активация темы
     *
     * @param string $themeName Название темы
     */
    public function activate_action($themeName) {
        if ($theme = $this->getThemes($themeName)) {
            $this->set($themeName);
            flash_success(t('Тема успешно активирована!'));
            redirect(l('/admin/theme'));
        }
    }

    /**
     * Возвращает установленные темы или указанную тему
     *
     * @param   $themeName  Название темы
     * @return  array|Theme
     */
    private function getThemes($themeName = '') {
        $files = glob(THEMES . DS . '*' . DS . 'info' . EXT);
        $themes = array();
        if ($files) {
            foreach ($files as $file) {
                $theme_dir = basename(dirname($file));
                $theme_class = $theme_dir . '_Theme';
                $theme = new $theme_class(new Config($file));
                if ($theme->theme == $themeName) {
                    return $theme;
                }
                $theme != $this->object() && array_push($themes, $theme);
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
     * Инициализация указанной темы
     *
     * @param string $theme
     * @param boolean $final
     */
    public function choose($theme) {
        $class = self::themeToClass($theme);
        if (!class_exists($class)) {
            error(t('Тема <b>%s</b> не существует.', $theme));
            $class = 'Default_Theme';
            $theme = 'Default';
            return $this->choose('Default');
        }
        $this->object(new $class(new Config(THEMES . DS . $theme . DS . 'info' . EXT)));
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
    cogear()->theme->region($name)->append($value);
}

function prepend($name, $value) {
    cogear()->theme->region($name)->prepend($value);
}

function inject($name, $value, $position = 0) {
    cogear()->theme->region($name)->inject($value, $position);
}

function theme($place) {
    return cogear()->theme->renderRegion($place);
}