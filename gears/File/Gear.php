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
        'form.element.editor.render' => 'hookFormEditorRender'
    );
    protected $routes = array(
//        'admin/files' => 'admin_action',
//        'files/upload' => 'index_action',
        'files/upload/(.+)' => 'upload_action',
//        'files/ajax/(\w+)/(\w+)' => 'ajax_action',
//        'files/?' => 'library_action',
    );
    protected $access = array(
        'admin' => array(1),
        'ajax' => array(1, 100),
        'index' => array(1, 100),
        'library' => array(1, 100),
        'upload' => array(1, 100),
    );

    /**
     * Конструктор
     */
    public function __construct($config) {
        parent::__construct($config);
        Form::$types['file'] = 'File_Element';
        Form::$types['file_url'] = 'File_Url_Element';
    }

//    /**
//     * Добавляем пункт в меню
//     *
//     * @param Menu $menu
//     */
//    public function hookMenuAdmin($menu) {
//        $menu->add(array(
//            'label' => icon('file') . ' ' . t('Файлы'),
//            'link' => l('/admin/files'),
//            'order' => 1000,
//        ));
//    }
//
//    public function hookLibraryMenu() {
//
//    }

    /**
     * Хук для вывода кнопки под редактором
     *
     * @param type $Editor
     */
    public function hookFormEditorRender($Editor) {
        $this->initUploader();
        $Editor->options->after->append(template('File/templates/hooks/editor')->render());
    }

//    /**
//     * Панель управления файлами
//     */
//    public function admin_action() {
//
//        $this->index_action();
//    }
//
//    /**
//     * Отображение панели файлов для пользователя
//     */
//    public function index_action() {
//        $this->initUploader();
//        $config = array(
//            'type' => 'image',
//            'allowed_types' => array('jpg', 'png', 'gif'),
//            'maxsize' => '300Kb',
//            'path' => user()->getUploadPath(),
//            'validate' => array('Required'),
//            'rewrite' => TRUE,
//            'preset' => 'image.large'
//        );
//        $tpl = new Template('File/templates/upload');
//        $tpl->filters = array(
//            array('extensions' => implode(',', $config['allowed_types']))
//        );
//        $tpl->config = $config;
//        $tpl->show();
//        $form = new Form(array(
//                    '#name' => 'file.upload',
//                    'file' => $config,
//                ));
//        if (isset($_FILES['file'])) {
//            $ajax = new Ajax();
//            if ($result = $form->result()) {
//                $file = new File();
//                $file->path = $result->file;
//                if (!$file->find()) {
//                    $file->getInfo();
//                }
//
//                $file->created_date = time();
//                $file->aid = user()->id;
//                $file->type = $file->getType();
//                $file->size = $file->info->getSize();
//                if ($file->save()) {
//                    $ajax->message(t('Файл <b>%s</b> успешно загружен на сервер!', $file->info->getBasename()));
//                }
//            } else {
//                $ajax->message($form->file->getErrors()->toString('<br/>'), 'error');
//            }
//            $ajax->json();
//        } else {
//            $this->list_action(user()->id, 'image');
//        }
//    }
//
    public function upload_action($type) {
        $ajax = new Ajax();
        $ajax->success = FALSE;
        switch ($type) {
            case 'editor/image':
                $form = new Form(array(
                            '#name' => 'file.upload.editor.image',
                            'file' => array(
                                'type' => 'image',
                                'allowed_types' => array('jpg', 'png', 'gif'),
                                'maxsize' => '300Kb',
                                'path' => Image::uploadPath(),
                                'validate' => array('Required'),
                                'rewrite' => TRUE,
                                'preset' => 'image.large')
                        ));
                if ($result = $form->result()) {
                    if($result->file){
                        $ajax->code = Image::getThumbCode($result->file,'image.medium');
                        $ajax->success = TRUE;
                    }
                }
                break;
        }
        $ajax->json();
    }
//
//    /**
//     * Отображение файлов конкретного пользователя
//     *
//     * @param type $uid
//     * @param type $type
//     */
//    public function library_action($uid, $type = NULL, $limit = NULL) {
//        $file = new File();
//        $file->aid = $uid;
//        $type && $file->type = $type;
//        $file->order('created_date', 'DESC');
//        $limit && $file->limit($limit);
//        if ($files = $file->findAll()) {
//            $tpl = template('File/templates/list', array('files' => $files))->show();
//        }
//    }
//
//    /**
//     * Аякс
//     *
//     * @param string $action
//     * @param mixed $param
//     */
//    public function ajax_action($action, $param = NULL) {
//        $ajax = new Ajax();
//        $ajax->success = FALSE;
//        switch ($action) {
//            case 'delete':
//                if ($param) {
//                    $file = new File();
//                    $file->id = $param;
//                    role() > 1 && $file->aid = user()->id;
//                    if ($file->find()) {
//                        $filename = $file->getInfo()->getBasename();
//                        if ($file->delete()) {
//                            File::remove(UPLOADS . DS . $file->path);
//                            $ajax->message(t('Файл <b>%s</b> успешно удалён!', $filename));
//                            $ajax->success = TRUE;
//                        }
//                    }
//                }
//                break;
//        }
//        $ajax->json();
//    }

    /**
     * Просто подключает нужные для загрузчика js-файлы
     */
    public function initUploader() {
        js($this->folder . '/js/plupload/uploader.js');
        js($this->folder . '/js/plupload/plupload.js');
        $i18n = '/js/plupload/i18n/' . config('lang.lang') . '.js';
        file_exists($this->dir . DS . $i18n) && js($this->folder . $i18n);
    }

}