<?php

/**
 * Шестеренка баз данных
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Db_Gear extends Gear {
    protected $hooks = array(
        'dev.trace' => 'hookTrace',
    );

    /**
     * Конструктор
     */
    public function __construct($xml) {
        parent::__construct($xml);
        $config = config('database');
        $this->object(Db::factory('default', $config, $config->driver));
    }

    /**
     * Вывод отладочной информации в подвал темы
     *
     * @param Stack $Stack
     */
    public function hookTrace() {
        echo template('Db/templates/trace',array('queries'=>$this->queries));
    }

}
