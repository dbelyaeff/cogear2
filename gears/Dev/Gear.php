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
    protected $order = -10000;
    protected $hooks = array(
    );
    protected $access = array(
        'index' => array(1, 100),
    );
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
        bench('done');
        $template = new Template('Dev/templates/results');
        append('footer', $template->render());
    }

    /**
     * Transform point to human readable form
     *
     * @param	array	$point
     * @return	array
     */
    public static function humanize($point, $measure = NULL) {
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
 * Humanize benchmark
 * 
 * @param type $point
 * @param type $measure
 * @return type
 */
function humanize_bench($point, $measure = NULL){
    return Dev_Gear::humanize($point,$measure);
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
