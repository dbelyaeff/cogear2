<?php

/**
 * Шестерёнка «Страницы»
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Pages_Gear extends Gear {

    protected $hooks = array(
        '404' => 'hook404',
//        'router.run' => 'hookRouterRun',
        'menu.admin' => 'hookAdminMenu',
        'parse' => 'hookParse',
        'done' => 'hookDone',
    );
    protected $routes = array(
        'admin/pages' => 'admin_action',
        'admin/pages/create' => 'admin_createdit',
        'admin/pages/(\d+)' => 'admin_createdit',
        'admin/pages/settings/?' => 'admin_settings_action',
        'admin/pages/ajax/(\w+)' => 'admin_ajax',
        'admin/pages/ajax/(\w+)/(\w+)' => 'admin_ajax',
    );
    protected $access = array(
        'admin' => array(1),
        'admin_createdit' => array(1),
        'admin_ajax' => array(1),
        'admin_settings' => array(1),
        'show' => TRUE,
        'index' => TRUE,
    );
    /**
     * Текущая страница
     * @var Page
     */
    public $current;

    /**
     * Обработка 404 ошибки
     *
     * Предложение пользователю создать страницу
     */
    public function hook404() {
        if ($this->hookRouterRun($this->Router, TRUE)) {
            flash('event.404', FALSE);
        } else {
            if (access('Pages.admin')) {
                append('content', template('Pages/templates/invitation'));
            }
        }
    }

    /**
     * Хук роутера
     *
     * Если мы находим в кеше или в базе занятый путь, вызываем Callback по нему
     *
     * @param object $Router
     */
    public function hookRouterRun($Router, $success = FALSE) {
        $uri = $Router->getUri();
        if ($route = route($uri, 'route')) {
            if ($callback = $route->decodeCallback($route->callback)) {
                $Router->exec($callback);
                return $success;
            }
        }
    }

    /**
     * Хук парсера
     *
     * @param object $item
     */
    public function hookParse($item) {
        if ($item->body && strpos($item->body, '[pagelist')) {
            preg_match_all('#\[pagelist(?:\s+root=(\d+)?)?\]#i', $item->body, $matches);
            for ($i = 0; $i < sizeof($matches[0]); $i++) {
                $root = empty($matches[1][$i]) ? 1 : $matches[1][$i];
                $item->body = str_replace($matches[0][$i], cogear()->pages->getList($root), $item->body);
            }
        }
    }

    /**
     * Создает элемент в админском меню
     *
     * @param Menu $menu
     */
    public function hookAdminMenu($menu) {
        $menu->add(array(
            'link' => l('/admin/pages'),
            'label' => icon('list') . ' ' . t('Страницы'),
            'order' => 2,
        ));
    }

    /**
     * Создаёт меню панели управления страницами
     *
     * На всякий случай метод сделан в виде хука
     */
    public function hookPagesAdminMenu() {
        new Menu_Tabs(array(
                    'name' => 'pages.admin',
                    'render' => 'info',
                    'elements' => array(
                        array(
                            'label' => icon('list') . ' ' . t('Список'),
                            'link' => l('/admin/pages'),
                            'active' => check_route('admin/pages', Router::ENDS),
                        ),
                        array(
                            'label' => icon('asterisk') . ' ' . t('Настройки'),
                            'link' => l('/admin/pages/settings'),
                            'active' => check_route('admin/pages/settings'),
                        ),
                        array(
                            'label' => icon('pencil') . ' ' . t('Создать'),
                            'link' => l('/admin/pages/create'),
                            'access' => check_route('admin/pages(/create)?'),
                            'class' => 'fl_r',
                        ),
                        array(
                            'label' => icon('pencil') . ' ' . t('Редактирование'),
                            'link' => l('/admin/pages/' . $this->router->getSegments(3)),
                            'active' => check_route('admin/pages/(\d+)'),
                            'access' => check_route('admin/pages/(\d+)'),
                            'class' => 'fl_r',
                        ),
                    ),
                ));
    }

    /**
     * Показ страницы
     *
     * @param type $id
     */
    public function show_action($id) {
        if ($page = page($id)) {
            $this->current = $page;
            $page->show();
        }
    }

    /**
     * Панель управления страницами
     */
    public function admin_action() {
        $this->hookPagesAdminMenu();
        $tree = new Db_Tree_DDList(array(
                    'class' => 'Pages_Object',
                    'saveUri' => l('/admin/pages/ajax/saveDBtree'),
                ));
    }

    /**
     * Главная страница
     */
    public function index_action() {
        $main_id = config('Pages.main_id', 1);
        if ($page = page($main_id)) {
            $this->current = $page;
            $page->show();
        } else {
            event('404');
        }
    }

    /**
     * Показывает страницы от корня
     *
     * @param type $root    корневая страница
     * @param boolean $render  делать ли вывод через шаблон
     */
    public function getList($root = 1, $render = TRUE) {
        if ($page = page($root)) {
            if ($render) {
                if (!$render = cache('pagelist.' . $root)) {
                    $tpl = new Template('Pages/templates/list');
                    $tpl->pages = $page->getChilds();
                    $render = $tpl->render();
                    cache('pagelist.'.$root,$render,array('pages'));
                }
                return $render;
            } else {
                return $page->getChilds();
            }
        }
    }

    /**
     * Создание страницы
     */
    public function admin_createdit($id = NULL) {
        $this->hookPagesAdminMenu();
        js($this->folder . '/js/inline/autolink_from_select.js');
        $form = new Form('Pages/forms/page');
        if ($id && $page = page($id)) {
            $page->link = $page->getLink();
            $form->object($page);
        } else {
            $page = page();
            $form->remove('delete');
            $form->pid->setValue($this->input->get('pid', 0));
        }
        $form->pid->setValues($page->getSelectValues());
        if ($result = $form->result()) {
            if ($result->delete) {
                if ($page->delete()) {
                    flash_success(t('Страница удалена вместе со всеми подстраницами!'));
                    redirect(l('/admin/pages'));
                }
            }
            // Заполняем объект страницы
            $page->object()->extend($result);
            $refresh = TRUE;
            // Если у страницы уже установлен путь, и он находится в базе
            if ($page->route) {
                // Если равны, то обновлять не надо
                $refresh = ($route->route != $page->getLink());
            }
            // Если путь не указан или не существует
            if ($refresh) {
                // Создаём новый путь
                $route = route();
                $route->route = trim($page->link, '/');
                // На всякий случай, если такой путь уже есть
                if (!$route->find()) {
                    // Сохраняем его
                    $route->insert();
                } else {
                    $route->update();
                }
                // Обновляем id
                $page->route = $route->id;
            }
            // Сохранение страницы
            if ($page->save()) {
                if ($refresh) {
                    $route->callback = $route->encodeCallback(array($this, 'show_action'), array($page->id));
                    $route->update();
                }
                flash_success(t('Страница <b>«%s»</b> успешно сохранена', $page->name), '', 'growl');
                redirect(l('/admin/pages'));
            }
        }
        $form->show();
    }

    /**
     * Редактирование настроек
     */
    public function admin_settings_action() {
        $this->hookPagesAdminMenu();
        $form = new Form(array(
                    'name' => 'admin.pages.settings',
                    'elements' => array(
                        'main_page' => array(
                            'label' => t('Главная страница'),
                            'type' => 'select',
                            'values' => page()->getSelectValues(),
                            'value' => config('Pages.main_id', 1),
                        ),
                        'actions' => array(
                            'elements' => array(
                                'save' => array(),
                            )
                        )
                    )
                ));
        if ($result = $form->result()) {
            if ($result->main_page) {
                $this->set('Pages.main_id', $result->main_page);
            }
        }
        $form->show();
    }

    /**
     * Ajax интерцептор
     *
     * @param string $action
     * @param mixed $param
     */
    public function admin_ajax($action = 'getLink', $param = NULL) {
        $ajax = new Ajax();
        switch ($action) {
            case 'getLink':
                if ($page = page($param)) {
                    $ajax->success = TRUE;
                    $ajax->link = trim($page->getLink(), '/');
                } else {
                    $ajax->success = FALSE;
                }
                break;
            case 'saveDBtree':
                if ($pages = $this->rebuildPagesTree($this->input->post('items'))) {
                    $ajax->success = TRUE;
                    $ajax->message(t('Структура сохранена!'));
                } else {
                    $ajax->success = FALSE;
                    $ajax->message(t('Не удалось сохранить структуру'), 'error', t('Ошибка'));
                }
                break;
        }
        $ajax->json();
    }

    /**
     * Перестраивает иерархиую страниц из полученного через Ajax массива вида:
     *
     * @param array $items
     */
    private function rebuildPagesTree($items, $parent_id = 0) {
        $i = 0;
        foreach ($items as $item) {
            $page = new Page();
            $page->id = $item['id'];
            if ($page->find()) {
                $page->pid = $parent_id;
                $page->branching(++$i);
                $page->update();
                if (isset($item['children'])) {
                    $this->rebuildPagesTree($item['children'], $page->id);
                }
            }
        }
        return TRUE;
    }

}

/**
 * Ярлык для страницы
 *
 * @param int $id
 * @param string    $param
 * @return  mixed
 */
function page($id = NULL, $param = 'id') {
    if ($id) {
        $page = new Page();
        $page->$param = $id;
        if ($page->find()) {
            return $page;
        }
    }
    return $id ? NULL : new Page();
}
