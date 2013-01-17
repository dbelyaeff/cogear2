<?php
/**
 * Класс элемента меню из базы данных
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Menu_Db_Item extends Db_Tree {
    protected $table = 'menu_items';
    protected $object_field = 'menu_id';
    /**
     * Вывод
     *
     * @param string $type
     * @return type
     */
    public function render($type = NULL) {
        switch ($type) {
            case 'admin.list':
                return template('Menu/Db/templates/item',array('item'=>$this))->render();
                break;
            default:
                return parent::render();
        }
    }
    /**
     * Переопределение метода insert
     *
     * @param array $data
     * @return boolean
     */
    public function insert($data = array()){
        if($result = parent::insert($data)){
            cogear()->cache->remove('menu.' . $this->menu_id . '.items');
        }
        return $result;
    }
    /**
     * Переопределение метода update
     *
     * @param array $data
     * @return boolean
     */
    public function update($data = array()){
        if($result = parent::update($data)){
            cogear()->cache->remove('menu.' . $this->menu_id . '.items');
        }
        return $result;
    }
    /**
     * Переопределение метода delete
     *
     * @return boolean
     */
    public function delete(){
        if($result = parent::delete($data)) {
            cogear()->cache->remove('menu.' . $this->menu_id . '.items');

        }
        return $result;
    }

}