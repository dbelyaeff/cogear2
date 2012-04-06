<?php

/**
 *  Benchmark Gear
 *
 *
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Benchmark
 * @subpackage
 * @version		$Id$
 */
class Dev_Gear extends Gear {

    protected $name = 'Developer';
    protected $description = 'Calculate cogear performance at current system configuration.';
    protected $order = 0;

    /**
     * Benchmark points
     *
     * @param
     */
    protected $points = array();

    /**
     * Initialization
     */
    public function init() {
        parent::init();
        $this->addPoint('system.begin');
        hook('done', array($this, 'finalPoint'), 0);
    }

    /**
     * Add benchmark info to user panel
     * 
     * @param   string  $name 
     * @param object $cp 
     */
    public function menu($name, &$cp) {
//        if($this->user->id != 1) return;
//        switch ($name) {
//            case 'user':
//                $cp->{Url::gear('dev')} =  t('Developer');
//                $cp->{Url::gear('dev')}->order = 98;
//                break;
//        }
    }

    /**
     * Add final point and show calculations for system benchmark
     */
    public function finalPoint() {
        $this->addPoint('system.end');
        if (access('development')) {
            $cogear = getInstance();
            $template = new Template('Dev.results');
            $template->data = Dev_Gear::humanize($cogear->dev->measurePoint('system'));
            append('footer', $template->render());
            js($this->folder . '/js/inline/debug.js');
        }
    }

    /**
     * Add point
     *
     * @param	string	$name
     */
    public function addPoint($name) {
        if (!isset($this->points[$name])) {
            $this->points[$name] = array(
                'time' => microtime() - IGNITE,
                'memory' => memory_get_usage(),
            );
        }
    }

    /**
     * Get points
     */
    public function getPoints($name = '') {
        if (!$name) {
            $this->addPoint('system.end');
            return $this->points;
        }
        else
            return isset($this->points[$name]) ? $this->points[$name] : NULL;
    }

    /**
     * Measure points
     * There should be two point. One with '.being' suffix, other with '.end'
     *
     * @param	string	$point
     */
    public function measurePoint($point) {
        $result = array();
        if (isset($this->points[$point . '.begin']) && isset($this->points[$point . '.end'])) {
            $result = array(
                'time' => $this->points[$point . '.end']['time'] - $this->points[$point . '.begin']['time'],
                'memory' => $this->points[$point . '.end']['memory'] - $this->points[$point . '.begin']['memory'],
            );
        }
        return $result;
    }

    /**
     * Transform point to human readable form
     *
     * @param	array	$point
     * @return	array
     */
    public static function humanize($point, $measure = null) {
        if (is_array($point) && !isset($point['time'])) {
            $result = array();
            foreach ($point as $key => $dot) {
                $result[$key] = self::humanize($dot, $measure);
            }
            return $result;
        }
        return array(
            'time' => self::microToSec($point['time']),
            'memory' => Filesystem::fromBytes($point['memory'], $measure),
        );
    }

    /**
     * Convert microtime to seconds
     *
     * @param	int	$microtime
     * @return	float
     */
    public static function microToSec($microtime) {
        return $microtime;
    }

}