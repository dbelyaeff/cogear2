<?php

/**
 * File gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class File_Gear extends Gear {

    protected $name = 'File';
    protected $description = 'Manage files';
    protected $package = '';
    protected $order = 0;
    protected $hooks = array(
//        'form.init.post' => 'hookPostForm',
    );

    /**
     * Construcotr
     */
    public function __construct() {
        parent::__construct();
        Form::$types['file'] = 'File_Element';
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



    public function index_action() {
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
                    $data['code'] .= template('File.attached',array('file'=>$file))->render();
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