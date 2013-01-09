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
    /**
     * Stop assets autoload
     */
    public function loadAssets() {
        //parent::loadAssets();
    }
    /**
     * Extend post form
     *
     * @param type $Form
     */
    public function hookPostForm($Form) {
        $Form->add('files', array(
            'type' => 'file',
            'order' => 3.1,
            'action' => l('/file/upload'),
            'multiple' => TRUE,
        ));
    }


    public function index_action(){
        $form = new Form(array(
            '#name' => 'file.upload',
            'file' => array(
                '#type' => 'file',
                '#label' => t('Файл'),
            ),
            'submit' => array(
                '#label' => t('Загрузить'),
            ),
        ));

        $form->show();
    }

    public function upload_action() {

    }

}