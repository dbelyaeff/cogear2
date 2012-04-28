<?php

/**
 * Date gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Date_Gear extends Gear {

    protected $name = 'Date';
    protected $description = 'Manage date and time';
    protected $package = '';
    protected $format;

    /**
     * Init
     */
    public function init() {
        parent::init();
        date_default_timezone_set(config('date.timezone', 'Europe/Moscow'));
        $this->format = config('date.format', 'H:i d.m.Y');
    }

    /**
     * Format date
     *
     * @param int $time
     * @param string $format
     * @return string
     */
    public function get($time, $format = NULL) {
        $date = date($format ? $format : $this->format, $time);
        event('date.format',$date);
        $month = date('F',$time);
        $date = str_replace($month, t($month,'Date.Month.Full'), $date);
        $short_month = date('M',$time);
        $date = str_replace($short_month, t($short_month,'Date.Month.Short'), $date);
        return $date;
    }

}

/**
 * Format date
 *
 * @param   int $time
 * @param   string  $format
 * @return  string
 */
function df($time, $format = NULL) {
    return cogear()->date->get($time, $format);
}