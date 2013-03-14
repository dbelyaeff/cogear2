<?php

/**
 * Шестеренка Панели управления
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Admin_Gear extends Gear {

    protected $routes = array(
        'admin' => 'dashboard_action',
        'admin/clear/(\w+)' => 'clear_action',
        'admin/site' => 'site_action',
        'admin/update' => 'admin_update',
        'admin/site/tools' => 'tools_action',
        'admin/site/export' => 'download_action',
    );
    protected $access = array(
        '*' => array(1),
    );
    protected $hooks = array(
        'menu' => 'hookMenu',
        'gear.request' => 'hookGearRequest',
    );
    public $bc;

    /**
     * Хук запроса к шестерёнке
     *
     * @param Object $Gear
     */
    public function hookGearRequest($Gear) {
        if ($Gear !== $this && 'admin' == $this->router->getSegments(0)) {
            $this->bc && $this->bc->add(array(
                        'link' => $this->router->getUri(),
                        'label' => $Gear->name,
                    ));
        }
    }

    /**
     * Выводим меню на странице настроек
     */
    public function hookSiteSettingsMenu() {
        $this->bc->add(array(
            'link' => l('/admin/site'),
            'label' => t('Сайт'),
        ));
        new Menu_Tabs(array(
            'name' => 'admin.site',
            'elements' => array(
                array(
                    'label' => t('Общие'),
                    'link' => l('/admin/site'),
                ),
                array(
                    'label' => icon('wrench') . ' ' . ('Инструменты'),
                    'link' => l('/admin/site/tools'),
                    'class' => 'fl_r',
                )
            )
                ));
    }

    /**
     * Initializer
     */
    public function init() {
        parent::init();
        if (access('Admin.*')) {
            new Menu_Auto(array(
                'name' => 'admin',
                'template' => 'Admin/templates/menu',
                'render' => 'before',
                    ));
            css($this->folder . '/css/menu.css', 'head');
            js($this->folder . '/js/menu.js', 'head');
            if ('admin' == $this->router->getSegments(0)) {
                $this->bc = new Breadcrumb_Object(
                        array(
                    'name' => 'admin.breadcrumb',
                    'title' => TRUE,
                    'titleActiveOnly' => FALSE,
                    'elements' => array(
                        array(
                            'link' => l('/admin'),
                            'label' => icon('home') . ' ' . t('Панель управления'),
                        ),
                    ),
                        ));
            }
        }
    }

    /**
     * Load assets - do not load everytime
     */
    public function loadAssets() {

    }

    /**
     * Получение запроса
     */
    public function request() {
        parent::request();
    }

    /**
     * Add Control Panel to user panel
     */
    public function hookMenu($name, $menu) {
        switch ($name) {
            case 'admin':
                $menu->add(array(
                    'label' => icon('home'),
                    'link' => l('/admin'),
                    'active' => check_route('admin$') OR check_route('^admin/dashboard'),
                    'order' => 0,
                    'elements' => array(
                        array(
                            'link' => l('/admin'),
                            'label' => icon('home') . ' ' . t('Главная'),
                        ),
                        array(
                            'link' => l('/admin/clear/session'),
                            'label' => icon('remove') . ' ' . t('Сбросить сессию'),
                            'order' => '0.1',
                        ),
                        array(
                            'link' => l('/admin/clear/cache'),
                            'label' => icon('trash') . ' ' . t('Сбросить кеш'),
                            'access' => access('Admin'),
                            'order' => '0.2',
                        )
                    ),
                ));
                $counter = config('admin.update.counter', 0);
                $menu->add(array(
                    'label' => icon('refresh') . ' ' . t('Обновления') . ($counter ? badge($counter) : ''),
                    'title' => t('Обновления'),
                    'link' => l('/admin/update'),
                    'order' => 1001,
                ));
                $menu->add(array(
                    'link' => l('/admin/site'),
                    'label' => icon('inbox') . ' ' . t('Сайт'),
                    'order' => 1000,
                ));
                break;
        }
    }

    /**
     * Показывает главную страницу панели управления
     */
    public function dashboard_action() {
        $panels = new Core_ArrayObject();
        $panels->system = new Core_ArrayObject(array(
            'span' => '6',
            'title' => t('О системе'),
            'content' => template('Admin/templates/dashboard/system')->render(),
                ));
        $panels->news = new Core_ArrayObject(array(
            'span' => '6',
            'title' => t('Новости проекта'),
            'content' => template('Admin/templates/dashboard/news')->caching(3600)->render(),
                ));
        event('admin.dashboard.panels', $panels);
        $tpl = new Template('Admin/templates/dashboard');
        $tpl->panels = $panels;
        $tpl->show();
    }

    /**
     * Ловушка для страницы по умолчанию
     */
    public function index_action() {
        redirect('/admin/gears');
    }

    /**
     * Обновление движка и шестерёнок
     */
    public function admin_update() {
        $major = 0;
        $show_main = TRUE;
        switch ($this->input->get('action')) {
            case 'check':
                $check_url = 'https://github.com/codemotion/cogear2/tags';
                $data = file_get_contents($check_url);
                preg_match_all('#/codemotion/cogear2/archive/v(.+?)\.zip#', $data, $matches);
                for ($i = 0; $i < sizeof($matches[0]); $i++) {
                    if (version_compare($matches[1][$i], $major) == 1) {
                        $major = $matches[1][$i];
                    }
                }
                $this->set('admin.update.lastcheck', time());
                $this->set('admin.update.repo.major', $major);
                break;
            case 'update_core':
                $version = config('admin.update.repo.major');
                if (version_compare($version, COGEAR) == 1) {
                    $link = 'https://github.com/codemotion/cogear2/archive/v'.$version.'.zip';
                    echo t('Загружаю архив с новой версией по адресу <i>%s</i>…', $link) . '<br/>';
                    if ($source = file_get_contents($link)) {
                        $archive = TEMP . DS . 'v' . $version . '.zip';
                        file_put_contents($archive, $source);
                        echo t('Архив загружен. Распаковываю…') . '<br/>';
                        $zip = new Zip($archive);
                        $folder_index = $zip->statIndex(0);
                        $folder = $folder_index['name'];
                        $zip->extract(TEMP . DS);
                        echo t('Архив распакован. Обновляю сайт…') . '<br/>';
                        $update_root = TEMP . DS . $folder;
                        $this->update_files($update_root, ROOT);
                        $show_main = FALSE;
                    } else {
                        echo t('Не удалось загрузить архив с системой!');
                    }
                } else {
                    echo t('Версия системы в репозитории не превышает версию установленной системы.');
                }
                echo '<p><a href="' . l('/admin/update') . '" class="btn">' . icon('arrow-left') . ' ' . t('Вернуться') . '</a>';
                break;
        }
        $show_main && template('Admin/templates/update/main')->show();
    }
    /**
     * Обновление файлов по указанному пути
     *
     * @param   $from   Путь источника
     * @param   $to     Путь назначения
     */
    private function update_files($from, $to) {
        $files = File::findByMask($from,'/(.+)/i');
        foreach($files as $file){
            $source_file = str_replace($from,'',$file);
            $orig_file = $to.DS.$source_file;
            if($source_file == 'config.php' OR $source_file == 'site.php'){
                continue;
            }
                if(file_put_contents(file_get_contents($file), $orig_file)){
                    echo t('Файл <i>%s</i> успешно обновлён…',$source_file);
                }
            echo $source_file.'<br/>';
        }
    }

    /**
     * Cleaner
     *
     * @param type $action
     */
    public function clear_action($action) {
        switch ($action) {
            case 'session':
                $this->session->remove();
                flash_success(t('Сессия сброшена'));
                break;
            case 'cache':
                flash_success(t('Системный кеш сброшен.'));
                $this->system_cache->clear();
                break;
        }
        back();
    }

    /**
     * Site config
     */
    public function site_action() {
        $this->hookSiteSettingsMenu();
        $front_values = new Core_ArrayObject(array(
            'Admin' => t('Панель управления'),
            'Post' => t('Пост'),
            'Pages' => t('Страницы'),
                ));
        $config = array(
            '#name' => 'admin-site',
            'name' => array(
                '#type' => 'text',
                '#label' => t('Название сайта'),
                '#validators' => array('Required'),
            ),
            'front_page' => array(
                '#type' => 'select',
                '#label' => t('Шестерёнка главной страницы:'),
                '#values' => $front_values,
                '#value' => config('router.defaults.gear')
            ),
            'dev' => array(
                '#type' => 'checkbox',
                '#label' => t('Режим разработки'),
                '#value' => config('development'),
            ),
            'save' => array(
            )
        );
        $form = new Form($config);
        $form->object(array(
            'name' => config('site.name'),
            'dev' => config('development'),
        ));
        if ($result = $form->result()) {
            $this->set('site.name', $result->name);
            $this->set('development', $result->dev);
            $this->set('router.defaults.gear', $result->front_page);
            flash_success(t('Настройки успешно сохранены!'));
            back();
        }
        $form->show();
    }

    /**
     * Выгрузка файла конфигурации
     */
    public function download_action($themes = array()) {
        $archive_name = 'config.zip';
        $path = TEMP . DS . $archive_name;
        $zip = new Zip(array(
            'file' => $path,
            'create' => TRUE,
                ));
        $zip->info(array('type' => 'config'));
        $zip->add(ROOT . DS . 'config' . EXT);
        $zip->close();
        File::download($path, $archive_name, TRUE);
    }

    /**
     * Импорт и экспорт
     */
    public function tools_action() {
        $this->hookSiteSettingsMenu();
        template('Admin/templates/tools')->show();
        $form = new Form('Admin/forms/import');
        if ($result = $form->result()) {
            if ($file = $result->file) {
                $zip = new Zip(array(
                    'file' => $file->path,
                    'check' => array('type' => 'config'),
                        ));

                if ($zip->extract(ROOT)) {
                    success(t('<b>Архив успешно распакован!</b> Новый файл конфигурации установлен.'));
                }
                $zip->close();
                unlink($file->path);
            }
        }
        $form->show();
    }

}
