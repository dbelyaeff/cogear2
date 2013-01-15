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
        'footer' => 'hookFooter',
        'done' => 'hookDone',
    );
    protected $routes = array(
        'admin/theme' => 'admin_action',
        'admin/theme/activate/(\w+)' => 'activate_action',
        'admin/theme/download' => 'download_action',
        'admin/theme/add' => 'upload_action',
        'admin/theme/widgets' => 'widgets_action',
        'admin/theme/widgets/(\d+)' => 'widgets_action',
        'admin/theme/widgets/(\d+)/(options)' => 'widgets_action',
        'admin/theme/widgets/(add)' => 'widgets_action',
        'admin/theme/widgets/(ajax)' => 'widgets_action',
    );
    protected $access = array(
        'admin' => array(1),
        'activate' => array(1),
        'download' => array(1),
        'upload' => array(1),
        'widgets' => array(1)
    );

    /**
     * Регионы в теме для вывода информации
     *
     * @var array
     */
    protected $regions = array(
        'head', 'before', 'header', 'info', 'content', 'sidebar', 'footer', 'after'
    );

    /**
     * Список классов виджетов с их названиями
     *
     * @var array
     */
    protected $widgets = array(
            // 'Theme_Widget_HTML' => 'Простой HTML код',
    );

    const SUFFIX = '_Theme';

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
        // Если Инсталлер включен, что шестерёнка Access выключена. Поэтмоу требуется проверка на существование функции
        if (function_exists('access') && access('Theme.admin') && $theme = $this->input->get('theme')) {
            return $this->choose($theme);
        }
        if ($Gear->options->theme) {
            $this->choose($Gear->options->theme);
        } else {
            $this->choose(config('theme.current', 'Default'));
        }
        if ('' === $this->input->get('splash')) {
            $this->template('Theme/templates/splash');
        } elseif ($tpl = $this->input->get('layout')) {
            $this->template($tpl);
        }
    }
    /**
     * Загружаем виджеты в конце. Чтобы все успело проинициализироваться.
     */
    public function hookDone() {
        // Загрузка виджетов
        $this->loadWidgets();
    }

    /**
     * Конструктор
     */
    public function __construct($config) {
        parent::__construct($config);
        $this->initRegions();
        // Почему здесь, а не выше в общем списке?
        // Потом что общий список инициализируется во время init.
        // Если указать там, то будут вовлечены только те шестерёнки, которые в очереди после данной идут
        hook('gear.init', array($this, 'hookGearInit'));
    }

    /**
     * Инициализация
     */
    public function init() {
        hook('callback.before', array($this, 'catchOutput'), NULL, 'start');
        hook('callback.after', array($this, 'catchOutput'), NULL, 'finish');
        parent::init();
    }

    /**
     * Хук инициализации шестерёнки
     *
     * @param type $Gear
     */
    public function hookGearInit($Gear) {
        if ($Gear->widgets) {
            foreach ($Gear->widgets as $class => $name) {
                $this->registerWidget($class, $name);
            }
        }
    }

    /**
     * Хук футера
     */
    public function hookFooter() {
        if (role() == 1) {
            echo template('Theme/templates/widgets/edit.link')->render();
        }
    }

    /**
     * Хук меню
     *
     * @param string  $name
     * @param object $menu
     */
    public function hookMenu($name, $menu) {
        switch ($name) {
            case 'admin':
                $menu->add(array(
                    'label' => icon('eye-open') . ' ' . t('Внешний вид'),
                    'link' => l('/admin/theme'),
                    'order' => 200,
                ));
        }
    }

    /**
     * Выводит меню в админке
     */
    public function hookAdminMenu($type = 1) {
        switch ($type) {
            case 1:
                new Menu_Tabs(array(
                            'name' => 'admin.theme',
                            'elements' => array(
                                array(
                                    'label' => t('Темы'),
                                    'link' => l('/admin/theme')
                                ),
                                array(
                                    'label' => t('Виджеты'),
                                    'link' => l('/admin/theme/widgets')
                                )
                            )
                        ));
                break;
            case 2:
                new Menu_Pills(array(
                            'name' => 'admin.theme.add',
                            'multiple' => TRUE,
                            'elements' => array(
                                array(
                                    'label' => icon('upload') . ' ' . t('Загрузить'),
                                    'link' => l('/admin/theme/add'),
                                    'access' => !check_route('admin/theme$'),
                                ),
                                array(
                                    'label' => icon('plus') . ' ' . t('Добавить'),
                                    'link' => l('/admin/theme/add'),
                                    'class' => 'fl_r'
                                )
                            ),
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
        $this->hookAdminMenu();
        $this->hookAdminMenu(2);
        $themes = $this->getThemes();
        foreach ($themes as $key => $theme) {
            if ($theme == $this->object()) {
                unset($themes[$key]);
            }
        }
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
     * Выгрузка тем
     */
    public function download_action($themes = array()) {
        if ($themes = $this->input->get('themes', $themes)) {
            !is_array($themes) && $themes = explode(',', $themes);
            // Если тема одна — называем архив её именем
            // Если тем несколько — называем архив gears
            $archive_name = (1 === sizeof($themes) ? end($themes) : 'themes') . '.zip';
            $path = TEMP . DS . $archive_name;
            $zip = new Zip(array(
                        'file' => $path,
                        'create' => TRUE
                    ));
            foreach ($themes as $theme) {
                $dir = THEMES . DS . $theme;
                // Если директория существует и шестерёнка не относится к ядру
                $zip->add($dir);
            }
            $zip->info(array(
                'type' => 'themes',
                'themes' => $themes
            ));
            $zip->close();
            File::download($path, $archive_name, TRUE);
        }
    }

    /**
     * Загрузка тем
     */
    public function upload_action() {
        $this->hookAdminMenu();
        $this->hookAdminMenu(2);
        $form = new Form('Theme/forms/add');
        if ($result = $form->result()) {
            if ($file = $result->file ? $result->file : $result->url) {
                $zip = new Zip(array(
                            'file' => $file->path,
                            'check' => array('type' => 'themes'),
                        ));
                if ($zip->extract(THEMES)) {
                    $info = $zip->info();
                    success(t('<b>Архив успешно распакован!</b> <p>Он содержал в себе следующие темы: <ul><li>%s</li></ul>', implode('</li><li>', $info['themes'])), '', 'content');

                    $zip->close();
                }
                unlink($file->path);
            }
        }
        $form->show();
    }

    /**
     * Загрузка виджетов
     */
    public function loadWidgets() {
        // Важно! Кэшируем только пары ключ => путь
        // Чтобы лишнего не хранить
        if (TRUE) {//!$widgets = cache('widgets')) {
            $widget = widget();
            $widget->order('position');
            $result = $widget->findAll();
            $widgets = array();
            foreach ($result as $widget) {
                $widgets[$widget->id] = $widget->route;
            }
            cache('widgets', $widgets);
        }
        if ($widgets) {
            foreach ($widgets as $id => $route) {
                if (check_route($route) && $widget = widget($id)) {
                    append($widget->region, $widget->render());
                }
            }
        }
    }

    /**
     * Управление виджетами
     *
     * @param mixed $action
     */
    public function widgets_action($action = 'list', $subaction = NULL) {
        new Menu_Tabs(array(
                    'name' => 'admin.theme.widgets',
                    'elements' => array(
                        array(
                            'label' => icon('list') . ' ' . t('Список'),
                            'link' => l('/admin/theme/widgets'),
                        ),
                        array(
                            'label' => icon('plus') . ' ' . t('Добавить'),
                            'link' => l('/admin/theme/widgets/add') . e('uri', $this->input->get('uri')),
                            'class' => 'fl_r',
                        ),
                        array(
                            'label' => icon('pencil') . ' ' . t('Редактировать'),
                            'link' => l('/admin/theme/widgets/' . $this->router->getSegments(3)),
                            'access' => check_route('widgets/(\d+)'),
                            'active' => check_route('widgets/(\d+)$'),
                            'class' => 'fl_r',
                        ),
                        array(
                            'label' => icon('wrench') . ' ' . t('Настройки'),
                            'link' => l('/admin/theme/widgets/' . $this->router->getSegments(3) . '/options'),
                            'access' => check_route('widgets/(\d+)'),
                            'active' => check_route('widgets/(\d+)/options'),
                            'class' => 'fl_r',
                        ),
                    )
                ));
        if ($action == 'ajax' && $widgets = $this->input->post('widgets')) {
            $ajax = new Ajax();
            $position = 0;
            foreach ($widgets as $config) {
                if ($widget = widget($config['id'])) {
                    $widget->region = $config['region'];
                    $widget->position = ++$position;
                    $widget->save();
                }
            }
            $this->cache->remove('widgets');
            $ajax->success = TRUE;
            $ajax->json();
        } else if ($action == 'list') {
            jqueryui();
            template('Theme/templates/widgets/search')->show();
            $widget = widget();
            $widgets = $widget->findAll();
            // Фильтруем, если задан параметр
            if ($uri = $this->input->get('uri')) {
                $remove_ids = array();
                foreach ($widgets as $key => $widget) {
                    if (!check_route($widget->route, $uri)) {
                        $remove_ids[] = $key;
                    }
                }
                foreach ($remove_ids as $id) {
                    $widgets->offsetUnset($id);
                }
            }
            template('Theme/templates/widgets/list', array(
                'regions' => $this->regions,
                'widgets' => $widgets,
            ))->show();
        } else {
            $form = new Form('Theme/forms/widget');
            $form->callback->setValues($this->getWidgets());
            $form->region->setValues(array_combine($this->regions, $this->regions));
            if (is_numeric($action) && $widget = widget($action)) {
                if ($subaction === 'options') {
                    template('Theme/templates/widgets/options.header', array('widget' => $widget))->show();
                    $class = $widget->callback;
                    $current_widget = new $class(unserialize($widget->object()->options));
                    $current_widget->object($widget);
                    if ($current_widget->settings()) {
                        $this->cache->remove('widgets');
                        flash_success(t('Настройки виджета <b>%s</b> сохранены!', $widget->name), '', 'growl');
                        redirect(l(TRUE));
                    }
                    return;
                }
                $form->object($widget);
                $form->callback->options->disabled = TRUE;
            } elseif ($action == 'add') {
                if ($uri = $this->input->get('uri')) {
                    $form->route->setValue($uri);
                }
                if ($region = $this->input->get('region')) {
                    $form->region->setValue($region);
                }
                $form->remove('delete');
                $widget = widget();
            } else {
                return event('empty');
            }
            if ($result = $form->result()) {
                $this->cache->remove('widgets');
                if ($result->delete && $widget->delete()) {
                    flash_success(t('Виджет «<b>%s</b>»успешно удалён!', $widget->name), '', 'growl');
                    redirect(l('/admin/theme/widgets'));
                }
                if ($action == 'add') {
                    $result->position = 1 + widget()->where('region', $result->region)->countAll();
                }
                $widget->object()->extend($result);
                if ($widget->save()) {
                    if ($action == 'add') {
                        flash_success(t('Виджет «<b>%s</b>» успешно добавлен!', $widget->name), '', 'growl');
                        redirect(l('/admin/theme/widgets/' . $widget->id . '/options'));
                    } else {
                        flash_success(t('Виджет  «<b>%s</b>» успешно отредактирован!', $widget->name));
                        redirect(l(TRUE));
                    }
                }
            }
            $form->show();
        }
    }

    /**
     * Регистрирует виджет в списке доступных
     *
     * @param string $class
     * @param string $name
     */
    public function registerWidget($class, $name) {
        $this->widgets[$class] = $name;
    }

    /**
     * Возвращает классы виджетов
     *
     * @return  array
     */
    public function getWidgets() {
        return $this->widgets;
    }

    /**
     * Возвращает установленные темы или указанную тему
     *
     * @param   $themeName  Название темы
     * @return  array|Theme
     */
    public function getThemes($themeName = '') {
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
                array_push($themes, $theme);
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
            error(t('Темы <b>%s</b> не существует.', $theme));
            $class = 'Default_Theme';
            $theme = 'Default';
            return $this->choose('Default');
        }
        $config = new Config(THEMES . DS . $theme . DS . 'info' . EXT);
        $config->regions && $this->initRegions($config->regions);
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
     * Output
     */
    public function output() {
        $this->object()->render();
    }

    /**
     * Инициализация регионов
     */
    public function initRegions($regions = array()) {
        $regions OR $regions = $this->regions;
        foreach ($regions as $key => $region) {
            $this->offsetSet($region, new Theme_Region($region));
        }
    }

}

function append($region, $value) {
    cogear()->theme->$region->append($value);
}

function prepend($region, $value) {
    cogear()->theme->$region->prepend($value);
}

function inject($region, $value, $position = 0) {
    cogear()->theme->$region->inject($value, $position);
}

function theme($region) {
    if (cogear()->theme->$region instanceof Theme_Region) {
        return cogear()->theme->$region->render();
    }
    exit(t('Регион <b>%s</b> не определён в настройках темы', $region));
}

function widget($value = NULL, $param = 'id') {
    if (NULL === $value) {
        return new Theme_Widget();
    } else {
        $widget = new Theme_Widget();
        $widget->$param = $value;
        if ($widget->find()) {
            return $widget;
        }
        return NULL;
    }
}