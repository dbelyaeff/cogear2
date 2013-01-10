<?php

/**
 * Шестеренка Изображение
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Image_Gear extends Gear {

    protected $hooks = array(
        'markitup.toolbar' => 'hookMarkItUp',
        'form.render' => 'hookFormRender',
        'post.full.after' => 'hookPostComments',
    );
    protected $enabled = TRUE;

    /**
     * Конструктор
     */
    public function __construct($config) {
        parent::__construct($config);
        Form::$types['image'] = 'Image_Element';
    }

    /**
     * Hook form render
     *
     * @param type $Form
     */
    public function hookFormRender($Form) {
        switch ($Form->options->name) {
            case 'post':
            case 'page':
            case 'comment':
                if (access('images.upload')) {
                    $tpl = new Template('Image/templates/upload');
                    $tpl->show('after');
                    js($this->folder . '/js/markitup.js');
                }
                break;
        }
    }

    /**
     * Hook post comments
     */
    public function hookPostComments($after) {
        if ($after->object()->allow_comments) {
            if (access('images.upload')) {
                $tpl = new Template('Image/templates/upload');
                $tpl->show('after');
                js($this->folder . '/js/markitup.js');
            }
        }
    }

    /**
     * Extend MarkItUp toolbar
     *
     * @param type $toolbar
     */
    public function hookMarkItUp($toolbar) {
        if (access('images.upload')) {
            $toolbar->markupSet->append(array(
                'name' => t('Upload images'),
                'key' => 'G',
                'className' => 'markItUpImageUpload',
                'call' => 'showImageUpload',
            ));
        }
    }


    /**
     *
     */
    public function index_action() {
//        $image = new Image(UPLOADS.DS.'1.jpg');
//        $image->resize('128','128','crop')->save(UPLOADS.DS.'3.jpg');
////        append('content','<img src="/uploads/1.jpg"/>');
////        append('content','<img src="/uploads/2.jpg"/>');
//        append('content','<img src="/uploads/3.jpg"/>');
    }

    /**
     * Access
     */
    public function upload_action() {
        if (Ajax::is() && $_FILES['images']) {
            $image = new Image_Upload(array(
                        'name' => 'images',
                        'allowed_types' => array('png', 'jpg', 'gif'),
                        'maxsize' => '100Kb',
                        'overwrite' => TRUE,
                        'path' => Image::uploadPath(),
                        'preset' => 'post.large',
                    ));
            $files = $image->upload();
            $data = array();
            $ajax = new Ajax();
            if ($image->uploaded) {
                $data['success'] = TRUE;
                $data['code'] = '';
                foreach ($files as $file) {
                    if ($file->uri_full) {
                        $data['code'] .= template('Image/templates/insert', array('image' => $file))->render();
                    }
                }
            } else {
                $data['success'] = FALSE;
                if ($image->errors) {
                    $data['messages'][] = array(
                        'type' => 'error',
                        'body' => implode('<br/>', $image->errors),
                    );
                }
            }
            $ajax->json($data);
        }
    }

}

function image_preset($name, $path, $toUri = FALSE) {
    $preset = new Image_Preset($name);
    if ($preset->load()) {
        $image = $preset->image($path)->render();
        return $toUri ? Url::toUri($image) : $image;
    }
}