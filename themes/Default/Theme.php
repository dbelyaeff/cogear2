<?php
/**
 *  Default Theme gear
 *
 *
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Theme
 * @subpackage  	Default
 * @version		$Id$
 */
class Default_Theme extends Theme{
    protected $name = 'Default Theme';
    protected $description = 'Default engine theme.';
    /**
     * Init
     */
    public function init(){
        parent::init();
        hook('header',array($this,'renderLogo'));
    }
    /**
     * Render site logo
     */
    public function renderLogo(){
        if($logo = config('theme.logo')){
            echo HTML::a(Url::link(),HTML::img(Url::toUri(UPLOADS.$logo),config('site.name')));
        }
    }
}