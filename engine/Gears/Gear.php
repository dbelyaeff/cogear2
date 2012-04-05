<?php

/**
 * Gears manager
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Gears
 * @version		$Id$
 */
class Gears_Gear extends Gear {

    protected $name = 'Gears manager';
    protected $description = 'Manage and download gears.';
    protected $type = Gear::CORE;
    protected $package = 'Gears';
    protected $order = 0;

    public function menu($name, &$menu) {
        switch ($name) {
            case 'admin':
                $menu->{'gears'} = t('Gears');
                break;
            case 'tabs_gears':
                $all_gears = $this->getAllGears();
                $active_gears = $this->getActiveGears();
                $inactive_gears = array_diff($all_gears, $active_gears);
                $all_count = sizeof($all_gears);
                $active_count = sizeof($active_gears);
                $inactive_count = sizeof($inactive_gears);
                $menu->{'/'} = t('Active') . ' (' . $active_count . ')';
                $menu->{'all'} = t('All') . ' (' . $all_count . ')';
                $menu->{'inactive'} = t('Inactive') . ' (' . $inactive_count . ')';
                $menu->{'new'} = t('New');
                $menu->{'updates'} = t('Updates');
                $menu->{'add'} = t('Add');
                break;
        }
    }

    public function admin($action = 'active') {
        new Menu_Tabs('gears', Url::gear('admin') . 'gears');
        d('Admin Gears');
        $all_gears = $this->getAllGears();
        $active_gears = $this->getActiveGears();
        $inactive_gears = array_diff($all_gears, $active_gears);

        $doaction = NULL;
        if (!empty($_REQUEST['action-top']))
            $doaction = $_REQUEST['action-top'];
        if (!empty($_REQUEST['action-bottom']))
            $doaction = $_REQUEST['action-bottom'];
        if (!empty($_REQUEST['action']))
            $doaction = $_REQUEST['action'];
        if ($doaction && isset($_REQUEST['gears'])) {
            $gears = $this->filter_gears($_REQUEST['gears']);
            switch ($doaction) {
                case 'activate':
                    $this->activate_gears($gears);
                    break;
                case 'deactivate':
                    $this->deactivate_gears($gears);
                    break;
                case 'update':
                    $this->update_gears($_REQUEST['gears']);
                    break;
            }
            back();
        }
        switch ($action) {
            case 'index':
            case 'active':
                $gears = array();
                foreach ($active_gears as $gear => $class) {
                    if (class_exists($class)) {
                        $object = new $class;
                        $object->active = TRUE;
                        $gears[$object->package][$gear] = $object;
                    }
                }
                $tpl = new Template('Gears.list');
                $tpl->packages = $gears;
                $tpl->link = Url::gear('admin') . '/gears';
                append('content', $tpl->render());
                break;
            case 'all':
                $gears = array();
                foreach ($all_gears as $gear => $class) {
                    if (class_exists($class)) {
                        $object = new $class;
                        $object->active = ($object->package == 'Core' OR $object->type == Gear::CORE OR in_array($gear, array_keys($active_gears)));
                        $gears[$object->package][$gear] = $object;
                    }
                }
                $tpl = new Template('Gears.list');
                $tpl->packages = $gears;
                $tpl->link = Url::gear('admin') . '/gears';
                append('content', $tpl->render());
                break;
            case 'inactive':
                $gears = array();
                foreach ($inactive_gears as $gear => $class) {
                    if (class_exists($class)) {
                        $object = new $class;
                        $object->active = ($object->package == 'Core' OR $object->type == Gear::CORE OR in_array($gear, array_keys($active_gears)));
                        $gears[$object->package][$gear] = $object;
                    }
                }
                $tpl = new Template('Gears.list');
                $tpl->packages = $gears;
                $tpl->link = Url::gear('admin') . '/gears';
                append('content', $tpl->render());
                break;
            case 'new':
                $gears = array();
                $new_period = 60 * 60 * 7; // Gears that has been updated last week are to be new
                foreach ($all_gears as $gear => $class) {
                    $object = new $class;
                    if (time() - $object->file->getMTime() <= $new_period) {
                        if (!$object->active = ($object->package == 'Core' OR $object->type == Gear::CORE OR in_array($gear, array_keys($active_gears)))) {
                            $gears[$object->package][$gear] = $object;
                        }
                    }
                }
                $tpl = new Template('Gears.list');
                $tpl->packages = $gears;
                $tpl->link = Url::gear('admin') . '/gears';
                append('content', $tpl->render());
                break;
        }
    }

    /**
     * Gears dispatcher
     * 
     * @param string $action
     * @param string $gear 
     */
    public function index($action=NULL, $gear = NULL) {
        if (!access('admin gears')) {
            Ajax::denied();
        }
        switch ($action) {
            case 'activate':
                cogear()->activate($gear);
                $tpl = new Template('Gears.item');
                $tpl->assign($this->$gear->info());
                Ajax::json(array(
                    'items' => array(
                        array(
                            'id' => 'gear-' . $gear,
                            'action' => 'replace',
                            'code' => $tpl->render(),
                        )
                    )
                ));
                break;
            case 'deactivate':
                cogear()->deactivate($gear);
                $tpl = new Template('Gears.item');
                $name = strtolower($gear);
                $tpl->assign($this->$name->info());
                Ajax::json(array(
                    'items' => array(
                        array(
                            'id' => 'gear-' . $gear,
                            'action' => 'replace',
                            'code' => $tpl->render(),
                        )
                    )
                ));
                break;
        }
    }

    /**
     * Activate gears
     * 
     * @param   array   $gears
     */
    private function activate_gears($gears) {
        $result = array();
        foreach ($gears as $gear) {
            $this->activate($gear);
            $result[] = t($gear, 'Gears');
        }
        $result && flash_success(t('Following gears were activated: ') . '<b>' . implode('</b>, <b>', $result) . '</b>.');
    }

    /**
     * Deactivate gears
     * 
     * @param   array   $gears
     */
    private function deactivate_gears($gears) {
        $result = array();
        foreach ($gears as $gear) {
            $this->deactivate($gear);
            $result[] = t($gear, 'Gears');
        }
        $result && flash_success(t('Following gears were deactivated: ') . '<b>' . implode('</b>, <b>', $result) . '</b>.');
    }

    /**
     * Update gears
     * 
     * @param   array   $gears
     */
    private function update_gears($gears) {
        $result = array();
        foreach ($gears as $gear) {
            $this->update($gear);
            $result[] = t($gear, 'Gears');
        }
        $result && flash_success(t('Following gears were updated: ') . '<b>' . implode('</b>, <b>', $result) . '</b>.');
    }

    /**
     * Filter gears
     * 
     * You can't activate/deactivate/delete Core gears.
     * Also only one theme can be activate at the moment
     * 
     * @param array $gears
     * @return array 
     */
    private function filter_gears($gears) {
        $result = array();
        foreach ($gears as $gear) {
            $class = $gear . '_Gear';
            if (class_exists($class)) {
                $object = new $class;
                if ($object->type == Gear::CORE OR $object->package == 'Core') {
                    continue;
                }
                $result[] = $object->gear;
            }
        }
        return $result;
    }

}