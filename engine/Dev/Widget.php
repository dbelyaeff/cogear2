<?php

/**
 *  Benchmark widget
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Benchmark_Widget extends Pages_Widget {

    public function render() {
        $cogear = getInstance();
        $template = new Template('Benchmark.results');
        $template->data = Benchmark_Gear::humanize($cogear->benchmark->measurePoint('system'));
        return $template->render();
    }

    public function options() {
        
    }

}
