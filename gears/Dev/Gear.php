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
    protected $hooks = array(
    );
    protected $access = array(
        'index' => array(1, 100),
    );
    protected $is_core = TRUE;

    /**
     * Benchmark points
     *
     * @param
     */
    protected $points = array();

    public function __construct() {
        parent::__construct();
        $this->addPoint('system.begin');
    }

    /**
     * Init
     */
    public function init() {
        parent::init();
        if (access('Dev') && config('site.development')) {
            hook('done', array($this, 'finish'));
        }
    }

    /**
     * Add final point and show calculations for system benchmark
     */
    public function finish() {
        $this->addPoint('system.end');
        $cogear = getInstance();
        $template = new Template('Dev.results');
        $template->data = Dev_Gear::humanize($cogear->dev->calc('system'));
        append('footer', $template->render());
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
     * Measure points
     * There should be two point. One with '.being' suffix, other with '.end'
     *
     * @param	string	$point
     */
    public function calc($point) {
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
            'memory' => File::fromBytes($point['memory'], $measure),
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

/**
 * Temp debug
 *
 * @param type $data
 */
function debug($data, $type = FALSE) {
    echo '<pre class="well">';
    $type ? var_export($data) : print_r($data);
    echo '</pre>';
}
