<?php

class Db_Item extends Db_ORM implements Interface_Render {

    protected $template = 'Db.item';

    /**
     * Find all
     */
    public function findAll() {
        if ($result = parent::findAll()) {
            $class = $this->reflection->getName();
            foreach ($result as $key => $value) {
                $item = new $class($this->table);
                $item->attach($value);
                $result->$key = $item;
            }
        }
        return $result;
    }

    /**
     * Render
     * 
     * @param type $template 
     */
    public function render($template = NULL) {
        $template OR $template = $this->template;
        $tpl = new Template($template);
        $tpl->item = $this;
        return $tpl->render();
    }

}