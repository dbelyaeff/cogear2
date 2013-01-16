<?php
/**
 * Database Item
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011-2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Db_Item extends Db_ORM {

    protected $template = 'Db/templates/item';

    /**
     * Найти все
     */
    public function findAll() {
        if ($result = parent::findAll()) {
            $class = $this->reflection->getName();
            foreach ($result as $key => $value) {
                $item = new $class($this->table,$this->primary,$this->db);
                $item->object($value);
                $result->$key = $item;
            }
        }
        return $result;
    }

    /**
     * Вывод
     */
    public function render() {
        $tpl = new Template($this->template);
        $tpl->item = $this;
        return $tpl->render();
    }

}