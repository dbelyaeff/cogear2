<?php
/**
 * Breadcrumb object
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Breadcrumb_Object extends Menu_Auto {
    /**
     * Constructor
     *
     * @param type $options
     */
    public function __construct($options) {
        $options['template'] = 'Breadcrumb.breadcrumb';
        !isset($options['render']) && $options['render'] = 'info';
        parent::__construct($options);
    }

    /**
     * Build crumbs from menu active items
     *
     * @param Menu_Object $menu
     */
    public function update(SplSubject $menu){
        foreach($menu as $item){
            if($item->active){
                foreach($this as $sitem){
                    if($sitem->link == $item->link){
                        $stop = TRUE;
                    }
                }
                isset($stop) OR $this->append($item);
            }
        }
    }
}