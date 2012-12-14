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
     * Avoid assets autoload
     */
    public function loadAssets() {
        //parent::loadAssets();
    }

    /**
     *
     */
    public function index_action() {
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
                        'path' => File::mkdir($this->user->dir() . '/images/' . date('Y/m/d/')),
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