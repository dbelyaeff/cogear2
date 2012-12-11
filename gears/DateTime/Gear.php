<?php

/**
 * Шестеренка, управляющая датой и временем
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class DateTime_Gear extends Gear {

    protected $name = 'Дата и время';
    protected $description = 'Manage date and time';
    protected $package = 'Date';
    protected $format;
    protected $enabled = TRUE;
    protected $monthes = array();
    /**
     * Инициалиазция
     */
    public function init() {
        parent::init();
        date_default_timezone_set(config('date.timezone', 'Europe/Moscow'));
        $this->format = config('date.format', 'H:i d.m.Y');
        $this->monthes = array(
            1 => t('января'),
            2 => t('февраля'),
            3 => t('марта'),
            4 => t('апреля'),
            5 => t('мая'),
            6 => t('июня'),
            7 => t('июля'),
            8 => t('августа'),
            9 => t('сентября'),
            10 => t('октября'),
            11 => t('ноября'),
            12 => t('декабря'),
        );
    }

    /**
     * Format date
     *
     * @param int $time
     * @param string $format
     * @return string
     */
    public function get($time, $format = NULL) {
        if($time >= strtotime('-1 minutes')){
            return t('менее минуты назад');
        }
        elseif($time >= strtotime('-1 hours')){
            return t('%d (минуту|минут|минуты) назад',date('m')-date('m',strtotime(date('H-00',$time))));
        }
        elseif($time >= strtotime('today')){
            return t('Сегодня в %s',  date('H:i',$time));
        }
        else if($time >= strtotime('yesterday')){
            return t('Вчера в %s', date('H:i',$time));
        }
        else if($time > strtotime(date('01-01-Y'))) {
            return date('j',$time).' '.$this->monthes[date('n',$time)].' '.date(' в H:i',$time);
        }
        else {
            return date('j',$time).' '.$this->monthes[date('n',$time)].' '.date('Y в H:i',$time);
        }
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
    return cogear()->DateTime->get($time, $format);
}