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
        'gear.enable' => 'hookGearEnable',
        'gear.disable' => 'hookGearDisable',
        'done' => 'hookErrors',
    );

    /**
     * Хук на включение шестерёнки
     *
     * @param Gear $Gear
     * @param Core_ArrayObject $result
     */
    public function hookGearEnable($Gear, $result) {
        if ($result->success) {
            $install_dump = $Gear->getDir() . DS . 'install' . DS . 'install.sql';
            if (file_exists($install_dump)) {
                $this->import($install_dump);
            }
        }
    }

    /**
     * Хук на выключение шестерёнки
     *
     * @param Gear $Gear
     * @param Core_ArrayObject $result
     */
    public function hookGearDisable($Gear, $result) {
        if ($result->success) {
            $install_dump = $Gear->getDir() . DS . 'install' . DS . 'uninstall.sql';
            if (file_exists($install_dump)) {
                $this->import($install_dump);
            }
        }
    }

    /**
     * Отображает ошибки
     */
    public function hookErrors(){
        if($errors = $this->object()->getErrors()){
            error($errors->toString());
        }
    }

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
