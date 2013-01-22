<?php

/**
 * Класс страницы
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Pages_Object extends Db_Tree {

    protected $table = 'pages';
    protected $primary = 'id';
    protected $template = 'Pages/templates/page';

    /**
     * Отображение страницы
     *
     * @return type
     */
    public function show($region = NULL, $position = 0, $where = 0) {
        return parent::show($region, $position, $where);
    }

    /**
     * Получение ссылки на страницу
     *
     * @return string
     */
    public function getLink($type = 'default', $param = NULL) {
        switch ($type) {
            case 'edit':
                $uri = new Stack(array('name' => 'page.link.edit'));
                $uri->append('admin');
                $uri->append('pages');
                $uri->append($this->object()->id);
                break;
            default:
                if ($route = route($this->route, 'id')) {
                    return l('/' . $route->route);
                } else {
                    $uri = new Stack(array('name' => 'page.link.show'));
                    $uri->append('pages');
                    $uri->append('show');
                    $uri->append($this->id);
                }
        }
        return l('/' . $uri->render('/'));
    }
    /**
     * Вывод
     *
     * @param string $type
     * @return type
     */
    public function render($type = NULL) {
        switch ($type) {
            case 'admin.list':
                return template('Pages/templates/admin/item',array('item'=>$this))->render();
                break;
            default:
                return parent::render();
        }
    }

}