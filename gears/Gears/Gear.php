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
    protected $order = 0;
    protected $is_core = TRUE;

    /**
     * Menu hook
     *
     * @param string $name
     * @param object $menu
     */
    public function menu($name, $menu) {
        switch ($name) {
            case 'admin':
                $menu->register(array(
                    'link' => l('/admin/gears'),
                    'label' => icon('cog  icon-white') . ' ' . t('Gears', 'Gears.admin'),
                    'order' => 1,
                ));
                break;
        }
    }

    /**
     * Admin dispatcher
     *
     */
    public function admin($action = 'all') {
        $gears = new Gears(GEARS);
        if ($do = $this->input->post('do')) {
            $items = $this->input->post('gears');
            $do_gears = array_keys($items);
        } elseif ($do = $this->input->get('do')) {
            $do_gears = explode(',', $this->input->get('gears'));
        }
        if ($do) {
            $this->admin_action($do, $do_gears);
            return;
        }
        $menu = new Menu_Tabs(array(
                    'name' => 'gears',
                    'elements' => array(
                        array(
                            'label' => t('All') . ' (' . $gears->filter(Gears::EXISTS, TRUE)->count() . ')',
                            'link' => l('/admin/gears'),
                        ),
                        array(
                            'label' => t('Enabled') . ' (' . $gears->filter(Gears::ENABLED, FALSE)->count() . ')',
                            'link' => l('/admin/gears/enabled'),
                        ),
                        array(
                            'label' => t('Installed') . ' (' . $gears->filter(Gears::INSTALLED, FALSE)->count() . ')',
                            'link' => l('/admin/gears/installed'),
                        ),
                        array(
                            'label' => t('Uploaded') . ' (' . $gears->filter(Gears::EXISTS, FALSE)->count() . ')',
                            'link' => l('/admin/gears/uploaded'),
                        ),
                        array(
                            'label' => t('Add'),
                            'class' => 'fl_r',
                            'link' => l('/admin/gears/add'),
                        ),
                    ),
                ));
        if ($action == 'add') {
            return $this->admin_add();
        }
        switch ($action) {
            case 'enabled':
                $filter = Gears::ENABLED;
                break;
            case 'installed':
                $filter = Gears::INSTALLED;
                break;
            case 'uploaded':
                $filter = Gears::EXISTS;
                break;
        }
        isset($filter) && $gears = $gears->filter($filter, FALSE);
        if ($gears->count()) {
            $gears->ksort();
            $tpl = new Template('Gears/templates/table');
            $tpl->action = "";
            $tpl->gears = $gears;
            $tpl->show();
        } else {
            event('empty');
        }
    }

    /**
     *
     *
     * @param type $do
     * @param type $gears
     */
    public function admin_action($do, $do_gears) {
        $tpl = new Template('Gears/templates/action');
        $tpl->do = $do;
        $all_gears = new Gears(GEARS);
        $gears = new Core_ArrayObject();
        foreach ($do_gears as $key => $gear) {
            $gears->$key = $all_gears->$gear;
        }
        $tpl->gears = $gears;
        $tpl->show();
    }

    /**
     * Add gears
     */
    public function admin_add(){
        $form = new Form('Gears/forms/add');
        if($result = $form->result()){
            $file = $result->file ? $result->file : $result->url;
            if(TRUE === $this->zip->open($file->path)){
                $this->zip->extractTo(GEARS);
                $this->zip->close();
                success(t('Gears has been successfully installed!','Gears'));
            }
        }
        $form->show();
    }
}