<?php

/**
 * Класс меню из базы данных
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Menu_Db_Object extends Db_ORM_Options {

    protected $table = 'menu';
    public static $templates = array();

    /**
     * Регистриует шаблон
     *
     * @param string $template
     * @param string $description
     */
    public static function template($template, $description) {
        self::$templates[$template] = $description;
    }

    /**
     * Удаляем сразу вместе со всеми элементами
     *
     * @return boolean
     */
    public function delete() {
        if ($result = parent::delete()) {
            cogear()->cache->remove('menu.' . $this->id . '.items');
            $item = new Menu_Db_Item();
            $item->menu_id = $this->id;
            $item->delete();
        }
        return $result;
    }

    /**
     * Возвращает элементы меню
     *
     * @return array
     */
    public function getItems() {
        if (!$items = cache('menu.' . $this->id . '.items')) {
            $handler = new Menu_Db_Item();
            $hanlder->menu_id = $this->object()->id;
            $items = array();
            if ($result = $handler->findAll()) {
                foreach ($result as $item) {
                    $items[] = $item->object();
                }
            }
            cache('menu.' . $this->id . '.items', $items);
        }
        return $items;
    }

    /**
     * Вывод
     *
     * @return  string
     */
    public function render() {
        if (!$this->object()->options) {
            return '';
        }
        $menu = new Menu(array(
                    'name' => $this->object()->machine_name,
                    'template' => $this->object()->options->template,
                    'show_empty' => FALSE,
                    'render' => FALSE,
                    'multiple' => $this->object()->options->multiple,
                    'title' => $this->object()->options->title,
                    'titleActiveOnly' => TRUE,
                    'autoactive' => TRUE,
                ));
        if ($items = $this->getItems()) {
            foreach ($items as $item) {
                $menu->add(array(
                    'label' => $item->label,
                    'link' => $item->link,
                ));
            }
        }
        $tpl = new Template('Menu/Db/templates/menu');
        $tpl->menu = $menu;
        $tpl->object = $this->object();
        return $tpl->render();
    }

}