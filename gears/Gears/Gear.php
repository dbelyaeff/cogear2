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
    );
    protected $routes = array(
        'admin/gears/?' => 'admin_action',
        'admin/gears/status' => 'status_action',
        'admin/gears/download' => 'download_action',
        'admin/gears/add' => 'upload_action',
        'admin/gears/([a-z]+)' => 'admin_action',
    );
    protected $access = array(
        'admin' => array(1),
        'download' => array(1),
        'status' => array(1),
        'upload' => array(1),
    );

    /**
     * Menu hook
     *
     * @param string $name
     * @param object $menu
     */
    public function hookMenu($name, $menu) {
        switch ($name) {
            case 'admin':
                $menu->register(array(
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
                            'label' => icon('upload').' '.t('Загрузить'),
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
        if(!$do) $do = $this->input->post('do-alt');
        if($do){
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
            $zip = new ZipArchive();
            // Если шестерёнка одна — называем архив её именем
            // Если шестерёнок несколько — называем архив gears
            $archive_name = (1 === sizeof($gears) ? end($gears) : 'gears') . '.zip';
            $path = TEMP . DS . $archive_name;
            $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            foreach ($gears as $gear) {
                $dir = GEARS . DS . $gear;
                // Если директория существует и шестерёнка не относится к ядру
                if (is_dir($dir) && !cogear()->site->gears->findByValue($gear)) {
                    $files = File::findByMask($dir, '#^[^\.].+#');
                    foreach ($files as $file) {
                        $archive_file = str_replace(dirname($dir) . DS, '', $file);
                        $zip->addFile($file, $archive_file);
                    }
                }
            }
            $zip->setArchiveComment(base64_encode(serialize(array(
                'type' => 'gears',
                'gears' => $gears
            ))));
            $zip->close();
            File::download($path, $archive_name, TRUE);
        }
    }

    /**
     * Add gears
     */
    public function upload_action() {
        $this->hookAdminMenu();
        $this->hookAdminMenu(2);
        $form = new Form('Gears/forms/add');
        if ($result = $form->result()) {
            $file = $result->file ? $result->file : $result->url;
            $zip = new ZipArchive();
            if (TRUE === $zip->open($file->path)) {
                if($comment = $zip->getArchiveComment()){
                    if($info = unserialize(base64_decode($comment))){
                        if($info['type'] == 'gears'){
                            $zip->extractTo(GEARS);
                            success(t('<b>Архив успешно распакован!</b> <p>Он содержал в себе следующие шестерёнки: <ul><li>%s</li></ul>',implode('</li><li>',$info['items'])));
                        }
                        else error(t('Вы загружаете архив неверного формата!'),'','content');
                    }
                    else error(t('Неверно указана или отсутствует цифровая подпись архива. Принимаются только архивы, выгружденные через панель управления.'),'','content');
                }
                else error(t('Неверно указана или отсутствует цифровая подпись архива. Принимаются только архивы, выгружденные через панель управления.'),'','content');
                $zip->close();
            }
            unlink($file->path);
        }

        $form->show();
    }

}