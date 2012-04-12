<?php
/**
 * GoBack gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          
 * @version		$Id$
 */
class GoBack_Gear extends Gear {

    protected $name = 'GoBack';
    protected $description = 'Show "← Go Back" button where it is needed';
    protected $package = 'Utilities';

    /**
     * Init
     */
    public function init() {
        parent::init();
        hook('form.render',array($this,'showGoBackButton'));
    }
    
    /**
     * Show GoBack button hook
     */
    public function showGoBackButton($Form){
       $tpl = new Template('GoBack.button');
       $tpl->show();
    }
}