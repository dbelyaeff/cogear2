<?php

/**
 * Шестеренка, управляющая другими шестеренками
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Gears_Gear extends Gear {

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
                    'label' => icon('cog  icon-white') . ' ' . t('Шестеренки'),
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
        $gears = new Gears(GEARS,array(
            'remove' => FALSE,
            'charge' => TRUE
        ));
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
                            'label' => t('Все') . ' (' . $gears->count() . ')',
                            'link' => l('/admin/gears/'),
                        ),
                        array(
                            'label' => t('Активные') . ' (' . $gears->filter(Gears::ENABLED)->count() . ')',
                            'link' => l('/admin/gears/enabled'),
                        ),
                        array(
                            'label' => t('Неактивные') . ' (' . $gears->filter(Gears::DISABLED)->count() . ')',
                            'link' => l('/admin/gears/disabled'),
                        ),
                        array(
                            'label' => t('Ядро') . ' (' . $gears->filter(Gears::CORE)->count() . ')',
                            'link' => l('/admin/gears/core'),
                        ),
                        array(
                            'label' => t('Добавить'),
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
            case 'disabled':
                $filter = Gears::DISABLED;
                break;
            case 'core':
                $filter = Gears::CORE;
                break;
        }
        isset($filter) && $gears = $gears->filter($filter);
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
        $all_gears = new Gears(GEARS,array(
            'remove' => FALSE,
            'charge' => TRUE,
            'check' => TRUE,
        ));
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
    public function admin_add() {
        $form = new Form('Gears/forms/add');
        if ($result = $form->result()) {
            $file = $result->file ? $result->file : $result->url;
            if (TRUE === $this->zip->open($file->path)) {
                $this->zip->extractTo(GEARS);
                $this->zip->close();
                success(t('Шестерёнка была загружена!'));
            }
        }

        $form->show();
    }

}