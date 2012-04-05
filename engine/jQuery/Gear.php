<?php

/**
 * jQuery
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class jQuery_Gear extends Gear {

    protected $name = 'jQuery';
    protected $description = 'Add jQuery support to Cogear';
    protected $package = 'jQuery';
    protected $order = -1000;

    public function admin($action = NULL) {
//        if ($files = glob($this->dir . DS . 'js/jquery*')) {
//            $current_script = reset($files);
//            preg_match('#jquery-([\d.]+)(\.min)?\.js#', $current_script, $matches);
//            $current_version = $matches[1];
//            $code = file_get_contents('http://code.jquery.com/');
//            preg_match('#/jquery-([\d\.]+)\.min\.js#imsU', $code, $matches);
//            $server_script = 'http://code.jquery.com' . $matches[0];
//            $server_version = $matches[1];
//            if (version_compare($server_version, $current_version, '>')) {
//                if($action = 'update'){
//                    Filesystem::delete($current_script);
//                    copy($server_script,dirname($current_script).DS.basename($server_script));
//                    success(t('jQuery library has been update successfully!'));
//                }
//                else {
//                    info(t("Notice, jQuery framework has been updated to version <b>%s</b>. Current version is <b>%s</b>.", 'jQuery', $server_version, $current_version).'<a href="'.Url::link('admin/jquery/update/').'" class="button">'.t('Update').'</a>');
//                }
//            }
//        }
    }

}
