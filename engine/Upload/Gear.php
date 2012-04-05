<?php

/**
 * Upload gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Upload_Gear extends Gear {

    protected $name = 'Upload';
    protected $description = 'Upload files and images';
    protected $settings = array('theme'=>'Theme_Splash');

    public function index($action = NULL) {
        switch ($action) {
            case 'file':
                 $tpl = new Template('Upload.file');
                 $tpl->show();
                break;
            case 'image':
                $image = new Upload_Image('file',array('preset'=>'post','path'=>UPLOADS.DS.'posts'.DS.date('Y/m/d')));
                if($result = $image->upload()){
                    exit(HTML::img($result));
                }
                break;
            default:
                append('content', HTML::a(Url::gear('upload') . '/file?iframe', t('Upload'), array('rel' => 'modal', 'class' => 'button')));
        }
    }
}

