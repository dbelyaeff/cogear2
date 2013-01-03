<?php

/**
 * Шестерёнка Роутер
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Router_Gear extends Gear {
    /**
     * Конструктор
     */
    public function __construct($config){
        parent::__construct($config);
        $this->object(Router::getInstance());
    }
}
/**
 * Ярлык для объекта пути
 *
 * @param int $id
 * @param string    $param
 * @return  mixed
 */
function route($id = NULL, $param = 'id') {
    if ($id) {
        $route = new Router_Route();
        $route->$param = $id;
        if ($route->find()) {
            return $route;
        }
    }
    return $id ? NULL : new Router_Route();
}