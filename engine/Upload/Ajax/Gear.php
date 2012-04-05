<?php
/**
 * Ajax FileUpload gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Ajax
 * @version		$Id$
 */
class Upload_Ajax_Gear extends Gear {

    protected $name = 'Ajax FileUpload';
    protected $description = 'Handle ajax fileupload.';
    protected $type = Gear::MODULE;
    protected $package = 'Ajax';
    protected $order = 0;

    /**
     * Init
     */
    public function init() {
        parent::init();
        $cogear = getInstance();
        Form::$types['file'] = 'Upload_Ajax_Form_File';
        Form::$types['image'] = 'Upload_Ajax_Form_Image';
    }
    /**
     * Default dispatcher
     * 
     * @param string $action
     * @param string $subaction 
     */
    public function index($action = '', $subaction = NULL) {
            
    }
    
    /**
     * Custom dispatcher
     * 
     * @param   string  $subaction
     */
    public function action_index($subaction = NULL){
        
    }
}