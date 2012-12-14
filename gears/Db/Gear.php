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
    public function __construct($config) {
        parent::__construct($config);
        cogear()->db = $this;
    }

    /**
     * Инициализация
     */
    public function init() {
        $config = config('database');
        $db = Db::factory('system', $config);
        if ($db->connect()) {
            parent::init();
            $this->object($db);
        }
        hook('done', array($db->object(), 'showErrors'));
    }

    /**
     * Вывод отладочной информации в подвал темы
     *
     * @param Stack $Stack
     */
    public function hookTrace() {
        echo template('Db/templates/trace', array('queries' => $this->queries));
    }

}
