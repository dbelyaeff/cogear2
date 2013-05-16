<?php

/**
 * Шестеренка Меню
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Menu_Gear extends Gear {

    protected $hooks = array(
        'menu.admin.theme' => 'hookMenuAdminTheme',
        'parser' => 'hookParser',
        'form.init.page' => 'hookFormInitPage',
        'form.result.page' => 'hookFormResultPage',
    );
    protected $routes = array(
        'admin/theme/menu' => 'admin_action',
        'admin/theme/menu/(\d+)' => 'admin_action',
        'admin/theme/menu/(\d+)/items' => 'items_action',
        'admin/theme/menu/(\d+)/item/(\d+)' => 'items_action',
        'admin/theme/menu/(\d+)/item/(add)' => 'items_action',
        'admin/theme/menu/(create)' => 'admin_action',
        'admin/theme/menu/ajax/(\w+)' => 'ajax_action',
        'admin/theme/menu/ajax/(\w+)/(\d+)' => 'ajax_action',
    );
    protected $access = array(
        '*' => array(1),
    );

    /**
     * Хук обработки формы Страницы
     *
     * @param object $Form
     * @param boolean $is_valid
     * @param object $result
     */
    public function hookFormResultPage($Form, $is_valid, $result) {
        // Если идёт редактирование страницы, тогда удаляем поле
        if($Form->object() instanceof Page){
            $Form->remove('menu_item_autoadd');
        }
        // Если результат получен правильный(без ошибок в форме)
        elseif ($result) {
            // Если было выбрано значение (не равно 0) и такое меню существует в базе
            if ($result->menu_item_autoadd && $menu = menu($result->menu_item_autoadd)) {
                $menu_item = new Menu_Db_Item();
                $menu_item->menu_id = $menu->id;
                $menu_item->label = $result->name; // по умолчанию совпадает с именем страницы, но после можно исправить после при редактировании меню
                // Внимание! Создаём объект Page для получения ссылки, но не сохраняем его!
                $menu_item->link = l($result->link);
                $menu_item->save(); // Можно и insert(), но через save() система сама определяет вставлять новое значение или обновлять уже выбранное
            }
        }
    }

    /**
     * Переопределение формы Страницы
     *
     * @param object $Form
     */
    public function hookFormInitPage($Form) {
        // ORM-объект меню, которые хранятся в базе данных
        $menu_db = new Menu_Db();
        // Список значений элемента
        $values = array(
            0 => '',
        );
        //Добавляем значения из базы
        foreach ($menu_db->findAll() as $menu) {
            $values[$menu->id] = $menu->name;
        }
        $Form->add('menu_item_autoadd', array(
            // Тип поля
            'type' => 'select',
            // Название поля
            'label' => t('Создание пункта меню'),
            // Описание поле
            'description' => t('Если вы хотите, чтобы автоматически создавался пункт меню, выберите одно из существующих меню.'),
            // Значения поля
            'values' => $values,
            // По умолчанию
            'value' => 0,
            // Порядок поля в форме. Определяется опытным путём. Можно использовать дробные значения
            'order' => '7',
        ));
    }

    /**
     * Добавляем пункт меню на страниу админки «Внешний вид»
     *
     * @param Menu $menu
     */
    public function hookMenuAdminTheme($menu) {
        $menu->add(array(
            'label' => t('Меню'),
            'link' => l('/admin/theme/menu'),
            'title' => FALSE,
        ));
    }

    /**
     * Хук парсера
     *
     * @param object $item
     */
    public function hookParser($item) {
        if ($item->body && strpos($item->body, '[menu')) {
            preg_match_all('#\[menu=(\d+)\]#ism', $item->body, $matches);
            for ($i = 0; $i < sizeof($matches[0]); $i++) {
                if ($menu = menu($matches[1][$i])) {
                    $item->body = str_replace($matches[0][$i], $menu->render(), $item->body);
                }
            }
        }
    }

    /**
     * Выводит меню
     */
    public function hookAdminMenu() {
        new Menu_Tabs(array(
            'name' => 'admin.menu',
            'elements' => array(
                'list' => array(
                    'label' => icon('list') . ' ' . t('Все меню'),
                    'link' => l('/admin/theme/menu'),
                    'active' => check_route('admin/theme/menu$'),
                ),
                'new' => array(
                    'label' => icon('plus') . ' ' . t('Создать'),
                    'link' => l('/admin/theme/menu/create'),
                    'class' => 'fl_r'
                ),
                'edit' => array(
                    'label' => icon('pencil') . ' ' . t('Редактировать'),
                    'link' => l('/admin/theme/menu/' . $this->router->getSegments(3)),
                    'class' => 'fl_r',
                    'access' => check_route('admin/theme/menu/\d+'),
                ),
                'items' => array(
                    'label' => icon('list') . ' ' . t('Пункты'),
                    'link' => l('/admin/theme/menu/' . $this->router->getSegments(3) . '/items'),
                    'class' => 'fl_r',
                    'active' => check_route('admin/theme/menu/\d+/item'),
                    'access' => check_route('admin/theme/menu/\d+'),
                ),
            )
        ));
    }

    /**
     * Панель управления меню
     *
     * @param type $action
     */
    public function admin_action($action = NULL, $subaction = NULL, $param = NULL) {
        $this->theme->hookAdminMenu();
        $this->hookAdminMenu();
        if (!$action) {
            $menus = menu();
            $menus->order('position', 'ASC');
            if ($result = $menus->findAll()) {
                jqueryui();
                $tpl = new Template('Menu/Db/templates/list');
                $tpl->menus = $result;
                $tpl->show();
            } else {
                return event('empty');
            }
        } else {
            Menu_Db::template('Menu/templates/simple', t('Простой список'));
            Menu_Db::template('Bootstrap/templates/tabs', t('Табы Bootstrap'));
            Menu_Db::template('Bootstrap/templates/pills', t('Пилюли Bootstrap'));
            $form = new Form('Menu/forms/menu');
            $form->template->setValues(Menu_Db::$templates);
            $menu = new Menu_Db();
            if ($action == 'create') {
                $menu->object()->options = new Core_ArrayObject(array(
                    'template' => 'Bootstrap/templates/navbar',
                    'multiple' => 0,
                    'title' => 0,
                ));
                $form->remove('delete');
            } else {
                $menu->id = $action;
                if ($menu->find()) {
                    $form->object($menu);
                    $form->template->setValue($menu->object()->options->template);
                    $form->multiple->setValue($menu->object()->options->multiple);
                    $form->title->setValue($menu->object()->options->title);
                } else {
                    return event('empty');
                }
            }
            if ($result = $form->result()) {
                if ($result->delete && $menu->delete()) {
                    flash_success(t('Меню «%s» удалено!', $menu->name));
                    redirect(l('/admin/theme/menu'));
                }
                $menu->object()->options->template = $result->template;
                $menu->object()->options->multiple = $result->multiple;
                $menu->object()->options->title = $result->title;
                $menu->object()->name = $result->name;
                $menu->object()->machine_name = $result->machine_name;
                if ($menu->save()) {
                    flash_success($action == 'create' ? t('Меню «%s» создано успешно!', $menu->name) : t('Меню «%s» было успешно отредактировано!', $menu->name), '', 'growl');
                    redirect(l('/admin/theme/menu'));
                }
            }
            $form->show();
        }
    }

    /**
     * Диспатчер ajax-запросов
     *
     * @param string $action
     * @return JSON
     */
    public function ajax_action($action = NULL) {
        $ajax = new Ajax();
        switch ($action) {
            case 'saveItemsTree':
                if (!$menu = menu($param)) {
                    return event('empty');
                }
                if ($this->rebuildItemsTree($this->input->post('items'))) {
                    $ajax->success = TRUE;
                    $ajax->message(t('Структура сохранена!'));
                } else {
                    $ajax->success = FALSE;
                    $ajax->message(t('Не удалось сохранить структуру'), 'error', t('Ошибка'));
                }
                break;
            case 'order':
            default:
                if ($menus = $this->input->post('menus')) {

                    foreach ($menus as $id => $position) {
                        if ($menu = menu($id)) {
                            $menu->update(array('position' => $position));
                        }
                    }
                }
        }
        $ajax->json();
    }

    /**
     * Управление элементами отдельного меню
     *
     * @param mixed $id
     */
    public function items_action($menu_id, $id = NULL) {
        $this->theme->hookAdminMenu();
        $this->hookAdminMenu();
        if ($menu = menu($menu_id)) {
            append('content', '<div class="page-header"><h2>' . $menu->name . '</h2></div>');
        } else {
            return event('empty');
        }
        $pills = new Menu_Pills(array(
            'name' => 'admin.menu.items',
            'render' => FALSE,
            'elements' => array(
                array(
                    'label' => icon('list') . ' ' . t('Список пунктов'),
                    'link' => l('/admin/theme/menu/' . $menu_id . '/items'),
                ),
                array(
                    'label' => icon('plus') . ' ' . t('Добавить'),
                    'link' => l('/admin/theme/menu/' . $menu_id . '/item/add'),
                    'class' => 'fl_r'
                ),
                array(
                    'label' => icon('pencil') . ' ' . t('Редактировать'),
                    'link' => l('/admin/theme/menu/' . $menu_id . '/item/' . $this->router->getSegments(5)),
                    'access' => check_route('admin/theme/menu/\d+/item/\d+'),
                    'class' => 'fl_r'
                ),
            )
        ));
        append('content', $pills->render());
        if (NULL === $id) {
            $handler = new Menu_Db_Item();
            $handler->menu_id = $menu->id;
            if ($items = $handler->findAll()) {
                $tree = new Db_Tree_DDList(array(
                    'items' => $items,
                    'saveUri' => l('/admin/theme/menu/ajax/saveItemsTree/'),
                ));
            } else {
                return event('empty');
            }
        } else {
            $form = new Form('Menu/forms/item');
            $item = new Menu_Db_Item();
            if ($id != 'add' && is_numeric($id)) {
                $item->id = $id;
                if ($item->find()) {
                    $form->object($item);
                } else {
                    return event('empty');
                }
            } else {
                $form->remove('delete');
                $item->menu_id = $menu_id;
            }
            $form->pid->setValues($item->getSelectValues('label'));
            if ($result = $form->result()) {
                if ($result->delete && $item->delete()) {
                    flash_success(t('Элемент меню <b>«%s»</b> был удалён!', $item->label), '', 'growl');
                    redirect(l('admin/theme/menu/' . $menu_id . '/items'));
                }
                $item->object()->extend($result);
                if ($id !== 'add') {
                    $item->branching();
                }
                if ($item->save()) {
                    flash_success($id == 'add' ? t('Элемент меню <b>«%s»</b> успешно создан!', $item->label) : t('Элемент меню <b>«%s»</b> успешно отредактирован!', $item->label), '', 'growl');
                    redirect(l('admin/theme/menu/' . $menu_id . '/items'));
                }
            }
            $form->show();
        }
    }

    /**
     * Перестраивает иерархиую страниц из полученного через Ajax массива вида:
     *
     * @param array $items
     */
    private function rebuildItemsTree($items, $parent_id = 0) {
        $i = 0;
        foreach ($items as $item) {
            $menu_item = new Menu_Db_Item();
            $menu_item->id = $item['id'];
            if ($menu_item->find()) {
                $menu_item->pid = $parent_id;
                $menu_item->branching(++$i);
                $menu_item->update();
                if (isset($item['children'])) {
                    $this->rebuildItemsTree($item['children'], $menu_item->id);
                }
            }
        }
        return TRUE;
    }

}

/**
 * Ярлык для создания/поиска разных типов меню
 *
 * @param type $id
 * @return Menu|Menu_Db|null
 */
function menu($id = NULL) {
    if (is_array($id)) {
        return new Menu($id);
    } else if (NULL === $id) {
        return new Menu_Db();
    } else {
        $menu = new Menu_Db();
        $menu->id = $id;
        if ($menu->find()) {
            return $menu;
        } else {
            return NULL;
        }
    }
}
