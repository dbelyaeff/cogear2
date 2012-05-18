<?php

/**
 * Image gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Image
 * @subpackage
 * @version		$Id$
 */
class Image_Gear extends Gear {

    protected $name = 'Image';
    protected $description = 'Image processor.';
    protected $package = 'Images';
    protected $hooks = array(
        'markitup.toolbar' => 'hookMarkItUp',
        'form.render' => 'hookFormRender',
        'post.show.full.after' => 'hookPostComments',
    );

    /**
     * Hook form render
     *
     * @param type $Form
     */
    public function hookFormRender($Form) {
        switch ($Form->options->name) {
            case 'post':
            case 'comment':
                if (access('image.upload')) {
                    $tpl = new Template('Image.upload');
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
        if ($after->object->allow_comments) {
            if (access('image.upload')) {
                $tpl = new Template('Image.upload');
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
        if (access('image.upload')) {
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
                    if ($file->uri) {
                        $data['code'] .= template('Image.insert', array('image' => $file))->render();
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

}

function image_preset($name, $path, $toUri = FALSE) {
    $preset = new Image_Preset($name);
    if ($preset->load()) {
        $image = $preset->image($path)->render();
        return $toUri ? Url::toUri($image) : $image;
    }
}