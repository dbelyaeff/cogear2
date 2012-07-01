<?php

/**
 * Redactor gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Redactor_Gear extends Gear {

    protected $name = 'Redactor';
    protected $description = 'Redactor editor';
    protected $package = 'Wysiwyg';
    protected $order = 0;

    /**
     * Upload Image action
     *
     * @param type $type
     */
    public function upload_action($type = 'image') {
        switch ($type) {
            default:
                $img = new Image_Upload(array(
                            'name' => 'file',
                            'min' => array(
                                'width' => 50,
                                'height' => 50,
                            ),
                            'max' => array(
                                'width' => 1280,
                                'height' => 800,
                            ),
                            'preset' => 'post',
                            'path' => UPLOADS . DS . 'images' . DS . date('Y/m/d'),
                            'rename' => substr(md5(time() . cogear()->session->get('ip')), 0, 5) . substr(time(), 5, 10),
                        ));
                if ($result = $img->upload()) {
                    $ajax = new Ajax();
                    $info = $img->getInfo();
                    $ajax->append('<img src="' . File::pathToUri(UPLOADS . $result) . '" width="' . $info->width . '" height="' . $info->height . '" alt=""/>');
                    $ajax->send();
                }
                else
                    exit(implode('<br/>', $img->errors));
        }
    }

    /**
     * Link
     */
    public function link_action(){
        $form = new Form('Redactor.link');
        $form->show();
    }
    /**
     * Toolbar
     *
     * @param type $type
     */
    public function toolbar_action($type = 'post') {
        $ajax = new Ajax();
        d('Redactor');
        $toolbar = array(
            'html' => array(
                'title' => t('HTML'),
                'func' => 'toggle',
                'separator' => TRUE,
            ),
            'styles' => array(
                'title' => t('Styles'),
                'func' => 'show',
                'separator' => TRUE,
                'dropdown' => array(
                    'p' => array(
                        'title' => t('Paragraph'),
                        'exec' => 'formatblock',
                        'param' => '<p>',
                    ),
                    'blockquote' => array(
                        'title' => t('Quote'),
                        'exec' => 'formatblock',
                        'param' => '<blockquote>',
                    ),
                    'pre' => array(
                        'title' => t('Preformatted'),
                        'exec' => 'formatblock',
                        'param' => '<pre>',
                    ),
                    'h1' => array(
                        'title' => t('Header1'),
                        'exec' => 'formatblock',
                        'param' => '<h1>',
                    ),
                    'h2' => array(
                        'title' => t('Header2'),
                        'exec' => 'formatblock',
                        'param' => '<h2>',
                    ),
                    'h3' => array(
                        'title' => t('Header3'),
                        'exec' => 'formatblock',
                        'param' => '<h3>',
                    ),
                    'h4' => array(
                        'title' => t('Header4'),
                        'exec' => 'formatblock',
                        'param' => '<h4>',
                    ),
                ),
            ),
            'bold' => array(
                'title' => t('Bold'),
                'exec' => 'Bold',
                'param' => FALSE,
            ),
            'italic' => array(
                'title' => t('Italic'),
                'exec' => 'italic',
                'param' => FALSE,
            ),
            'underline' => array(
                'title' => t('Underline'),
                'exec' => 'underline',
                'param' => FALSE,
            ),
            'deleted' => array(
                'title' => t('Deleted'),
                'exec' => 'strikethrough',
                'param' => NULL,
            ),
            'bold' => array(
                'title' => t('Bold'),
                'exec' => 'Bold',
                'param' => NULL,
            ),
            'insertunorderedlist' => array(
                'title' => '&bull; ' . t('List'),
                'exec' => 'insertunorderedlist',
                'param' => null,
            ),
            'insertorderedlist' => array(
                'title' => '1. ' . t('List'),
                'exec' => 'insertorderedlist',
                'param' => null,
            ),
            'outdent' => array(
                'title' => '< ' . t('Outdent'),
                'exec' => 'outdent',
                'param' => null,
            ),
            'indent' => array(
                'title' => '< ' . t('indent'),
                'exec' => 'indent',
                'param' => null,
                'separator' => true,
            ),
            'link' => array(
                'title' => t('Insert link'),
                'dataType' => 'modal',
                'dataSource' => 'form-redactor-link',
                'href' => '/redactor/link',
                'separator' => TRUE,
            ),
            'justifyleft' => array(
                'exec' => 'JustifyLeft',
                'name' => 'JustifyLeft',
                'title' => t('Left'),
            ),
            'justifycenter' => array(
                'exec' => 'JustifyCenter',
                'name' => 'JustifyCenter',
                'title' => t('Center'),
            ),
            'justifyright' => array(
                'exec' => 'JustifyRight',
                'name' => 'JustifyRight',
                'title' => t('Right'),
                'separator' => TRUE,
            ),
            'fullscreen' => array(
                'title' => t('Fullscreen'),
                'func' => 'fullscreen',
            ),
        );
        event('toolbar.'.$type,$toolbar);
        $ajax->append("if (typeof RTOOLBAR == 'undefined') var RTOOLBAR = {};

RTOOLBAR['default'] = " . json_encode($toolbar));
        $ajax->send();
    }

}