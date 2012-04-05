<?php

/**
 * Icons gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Icons_Gear extends Gear {

    protected $name = 'Icons';
    protected $description = 'Icons manager.';
    protected $order = -100;
    protected $sets;
    const DEFAULT_SET = 'famfamfam';

    /**
     * Init
     */
    public function init() {
        parent::init();
        $this->sets = new Core_ArrayObject();
        $this->addSet(self::DEFAULT_SET, $this->dir . DS . 'sets' . DS . self::DEFAULT_SET);
    }

    /**
     * Add icons set
     * 
     * @param string $name
     * @param string $path
     * @param string $ext
     * @param string $size 
     */
    public function addSet($name, $path, $ext = 'png', $size='16x16') {
        $this->sets->$name = new Icons_Set($path, $ext, $size);
    }

    /**
     * Show icon
     *
     * @param   $name
     */
    public function show($name, $set = '') {
        $set OR $set = self::DEFAULT_SET;
        $this->sets->$set OR $this->addSet($set, $this->dir . DS . 'sets' . DS . $set);
        if ($src = $this->sets->$set->get($name)) {
            $size = explode('x', $this->sets->$set->size);
            return HTML::img($src, t($name, 'Icons'), array(
                        'width' => $size[0],
                        'height' => $size[1],
                    ));
        }
    }

}

function icon($name, $set = '') {
    return cogear()->icons->show($name, $set);
}