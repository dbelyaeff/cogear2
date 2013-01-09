<?php

/**
 * Шестеренка Файл
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class File_Gear extends Gear {
    protected $hooks = array(
        'menu.admin' => 'hookMenuAdmin',
    );
    protected $routes = array(
        'file' => 'index_action'
    );
    protected $access = array(
        '*' => array(1),
    );
    /**
     * Конструктор
     */
    public function __construct($config) {
        parent::__construct($config);
        Form::$types['file'] = 'File_Element';
        Form::$types['file_url'] = 'File_Url_Element';
    }

    public function hookMenuAdmin($menu){

    }




    public function index_action(){
        $form = new Form(array(
            '#name' => 'file.upload',
            'file' => array(
                '#type' => 'image',
                '#label' => t('Файл'),
                '#maxsize' => 500,
                '#allowed_types' => 'jpg',
                '#validate' => array('Required'),
            ),
            'submit' => array(
                '#label' => t('Загрузить'),
            ),
        ));
        if($result = $form->result()){

        }
        $form->show();
    }

    public function upload_action() {

    }

}