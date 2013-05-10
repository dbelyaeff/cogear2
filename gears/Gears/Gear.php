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

    protected $hooks = array(
        'menu' => 'hookMenu',
        'gear.init' => 'hookGearInit'
    );
    protected $routes = array(
        'admin/gears/?' => 'admin_action',
        'admin/gears/status' => 'status_action',
        'admin/gears/download' => 'download_action',
        'admin/gears/add' => 'upload_action',
        'admin/gears/update/([\w_-]+)' => 'update_action',
        'admin/gears/([a-z]+)' => 'admin_action',
    );
    protected $access = array(
        'admin' => array(1),
        'download' => array(1),
        'status' => array(1),
        'upload' => array(1),
        'update' => array(1),
    );
    /**
     * Конструктор
     *
     * @param type $config
     */
    public function __construct($config) {
        parent::__construct($config);
        hook('gear.init',array($this,'hookGearInit'));
    }
    /**
     * Хук инициализации шестерёнки
     *
     * @param object $Gear
     */
    public function hookGearInit($Gear) {
        if (cogear()->gears->Role && role() === 1 && $Gear->checkUpdate()) {
            info(t('Шестерёнка <b>%s</b> требует обновления. <a href="%s" class="btn btn-primary btn-mini">Обновить</a>',$Gear->name,l('/admin/gears/update/'.$Gear->gear)));
        }
    }

    /**
     * Menu hook
     *
     * @param string $name
     * @param object $menu
     */
    public function hookMenu($name, $menu) {
        switch ($name) {
            case 'admin':
                $menu->add(array(
                    'link' => l('/admin/gears'),
                    'label' => icon('cog') . ' ' . t('Шестеренки'),
                    'order' => 1,
                ));
                break;
        }
    }

    public function hookAdminMenu($type = 1) {
        switch ($type) {
            case 1:
                $gears = new Gears(GEARS, array(
                    'remove' => FALSE,
                    'charge' => TRUE
                        ));
                new Menu_Tabs(array(
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
                            'label' => icon('plus') . ' ' . t('Добавить'),
                            'class' => 'fl_r',
                            'link' => l('/admin/gears/add'),
                        ),
                    ),
                        ));
                return $gears;
                break;
            case 2:
                new Menu_Pills(array(
                    'name' => 'gears.add',
                    'elements' => array(
                        array(
                            'label' => icon('upload') . ' ' . t('Загрузить'),
                            'link' => l('/admin/gears/add'),
                        )
                    ),
                        ));
                break;
        }
    }

    /**
     * Admin dispatcher
     *
     */
    public function admin_action($action = 'all') {
        $gears = $this->hookAdminMenu(1);
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
            $tpl->gears = $gears;
            $tpl->show();
        } else {
            event('empty');
        }
    }

    /**
     * Активация или деактивация
     */
    public function status_action() {
        $do = $this->input->post('do');
        if (!$do)
            $do = $this->input->post('do-alt');
        if ($do) {
            $do_gears = $this->input->post('gears');
        } elseif ($do = $this->input->get('do')) {
            $do_gears = explode(',', $this->input->get('gears'));
        }
        if (!$do)
            return event('403');
        switch ($do) {
            case 'enable':
            case 'disable':
                $tpl = new Template('Gears/templates/status');
                $tpl->do = $do;
                $all_gears = new Gears(GEARS, array(
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
                break;
            case 'download':
                $this->download_action($do_gears);
                break;
        }
    }

    /**
     * Выгрузка шестерёнок
     */
    public function download_action($gears = array()) {
        if ($gears = $this->input->get('gears', $gears)) {
            !is_array($gears) && $gears = explode(',', $gears);
            // Если шестерёнка одна — называем архив её именем
            // Если шестерёнок несколько — называем архив gears
            $archive_name = (1 === sizeof($gears) ? end($gears) : 'gears') . '.zip';
            $path = TEMP . DS . $archive_name;
            $zip = new Zip(array(
                'file' => $path,
                'create' => TRUE,
                    ));
            foreach ($gears as $gear) {
                $dir = GEARS . DS . $gear;
                // Если директория существует и шестерёнка не относится к ядру
                if (is_dir($dir) && !cogear()->site->gears->findByValue($gear)) {
                    $zip->add($dir);
                }
            }
            $zip->info(array(
                'type' => 'gears',
                'gears' => $gears
            ));
            $zip->close();
            File::download($path, $archive_name, TRUE);
        }
    }

    /**
     * Загрузка тем
     */
    public function upload_action() {
        $this->hookAdminMenu();
        $this->hookAdminMenu(2);
        $form = new Form('Gears/forms/add');
        if ($result = $form->result()) {
            if ($file = $result->file ? $result->file : $result->url) {
                $zip = new Zip(array(
                    'file' => UPLOADS.$file,
                    'check' => array('type' => 'gears')
                        ));
                if ($zip->extract(GEARS)) {
                    $info = $zip->info();
                    success(t('<b>Архив успешно распакован!</b> <p>Он содержал в себе следующие шестерёнки: <ul><li>%s</li></ul>', implode('</li><li>', $info['gears'])), '', 'content');
                }
                $zip->close();
                unlink(UPLOADS.$file);
            }
        }
        $form->show();
    }

    /**
     * Обновление шестерёнки
     *
     * @param string $gear
     */
    public function update_action($gear) {
        $gears = new Gears(GEARS, array(
            'remove' => FALSE,
            'charge' => TRUE
                ));
        if ($gears->$gear) {
            template('Gears/templates/update', array('gear' => $gears->$gear))->show();
        }
        else
            redirect(l('/admin/gears'));
    }

}