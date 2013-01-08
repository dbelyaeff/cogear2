<?php

class Db_Item extends Db_ORM {

    protected $template = 'Db\templates\item';

    /**
     * Find all
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
     * Render
     */
    public function render() {
        $tpl = new Template($this->template);
        $tpl->item = $this;
        return $tpl->render();
    }

}