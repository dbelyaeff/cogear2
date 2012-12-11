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

    /**
     * Construcotr
     */
    public function __construct($xml) {
        parent::__construct($xml);
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
        $Form->addElement('files', array(
            'type' => 'file',
            'order' => 3.1,
            'action' => l('/file/upload'),
            'multiple' => TRUE,
        ));
    }



    public function upload_action() {
        $image = new Image_Upload(array(
                    'name' => 'images',
                    'allowed_types' => array('png', 'jpg', 'gif'),
                    'maxsize' => '100Kb',
                    'overwrite' => TRUE,
                    'path' => File::mkdir($this->user->dir() . '/images'),
                ));
        $files = $image->upload();
        $data = array();
        $ajax = new Ajax();
        if ($image->uploaded) {
            $data['success'] = TRUE;
            $data['code'] = '';
            foreach ($files as $file) {
                if ($file->uri) {
                    $data['code'] .= template('File/templates/attached',array('file'=>$file))->render();
                }
            }
        } else {
            $data['success'] = FALSE;
            foreach ($files as $file) {
                if ($file->errors) {
                    $data['messages'][] = array(
                        'type' => 'error',
                        'body' => implode('<br/>', $file->errors),
                    );
                }
            }
        }
        $ajax->json($data);
    }

}