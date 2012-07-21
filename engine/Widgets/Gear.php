<?php

/**
 * Widgets gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Widgets_Gear extends Gear {

    protected $name = 'Widgets';
    protected $description = 'Widgets description';
    protected $package = '';
    protected $order = 1000;
    protected $hooks = array(
        'gear.dispatch.after' => 'hookDispatchAfter',
        'theme.region' => 'hookThemeRegion',
    );
    protected $routes = array(
    );
    protected $access = array(
        'index' => 'access',
    );

    /**
     * Acccess
     *
     * @param string $rule
     * @param object $Item
     */
    public function access($rule, $Item = NULL) {
        switch ($rule) {
            default:
                if (NULL !== $this->input->get('widgets') && role() == 1) {
                    return TRUE;
                }
                break;
        }
        return FALSE;
    }

    /**
     * Hook after gear dispath
     */
    public function hookDispatchAfter($gear) {
        if ($this->input->get('splash') !== NULL)
            return;
        $widgets = new Core_ArrayObject();
        if ($gear->widgets !== NULL) {
            if ($gear->widgets) {
                foreach ($gear->widgets as $class) {
                    class_exists($class) && $widgets->append(new $class());
                }
            }
            else {
                append('sidebar',' ');
            }
        } else {
            event('widgets', $widgets);
        }
        $widgets->uasort('Core_ArrayObject::sortByOrder');
        foreach ($widgets as $widget) {
            $widget->show();
        }
//        $widget = widget();
//        if ($widgets = $widget->findAll()) {
//            foreach ($widgets as $widget) {
//                $widget->show($widget->region);
//            }
//        }
    }

    /**
     * Hook theme region
     */
    public function hookThemeRegion($Region, $output) {
        if (!access('Widgets')) {
            return;
        }
        $output->prepend('<div class="region shd" id="theme-region-' . $Region->name . '"><div class="region-name">' . $Region->name . '</div><div class="region-controls"><a href="' . l('/widgets/catalog/') . '" class="sh"><i class="icon icon-inbox" title="' . t('Widgets', 'Widgets') . '"></i></a>
        </div>');
        $output->append('</div>');
    }

    /**
     * Init
     */
    public function init() {
        parent::init();
    }

    /**
     * Hook menu
     *
     * @param string $name
     * @param object $menu
     */
    public function menu($name, $menu) {
        switch ($name) {

        }
    }

//    public function
    /**
     * Default dispatcher
     *
     * @param string $action
     * @param string $subaction
     */
    public function index($action = '', $id = NULL) {
        if (Ajax::is() && NULL !== $this->input->get('dispatcher')) {
            $ajax = new Ajax();
            $ajax->json(
                    array(
                        'action' => 'fancybox',
                        'options' => array(
                            'type' => 'iframe',
                            'width' => '50%',
                            'height' => '50%',
                            'href' => l(TRUE) . '?splash',
                        ),
                    )
            );
        }
        switch ($action) {
            case 'options':
                if ($id && $widget = widget($id)) {
                    $widget->options();
                }
                break;
            case 'edit':
                if ($id && $widget = widget($id)) {
                    $widget->edit();
                }
                break;
            case 'catalog':
            default:
                $catalog = new Widgets_Catalog();
                $catalog->show();
                $widget = new Blog_Widget();
                $widget->region = 'sidebar';
                $widget->save();
        }
    }

}

/**
 * Shortcut for widget
 *
 * @param string    $text
 * @param string    $param
 */
function widget($id = NULL, $param = 'id') {
    if ($id) {
        $widget = new Widgets_Widget();
        $widget->$param = $id;
        if ($widget->find()) {
            return $widget;
        } else {
            return FALSE;
        }
    }
    return new Widgets_Widget();
}