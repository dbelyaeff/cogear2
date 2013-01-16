<?php

/**
 * Интернационализация
 *
 * Перевод интерфейса системы на разные языки
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2010, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Lang_Gear extends Gear {

    protected $domains = array();
    protected $lang;
    protected $locale;
    protected $hooks = array(
//        'gear.init' => 'hookGearInit',
        'done' => 'hookDone',
        'menu' => 'hookMenu',
        'gear.enable' => 'hookGearEnable',
    );
    protected $routes = array(
        'admin/lang' => 'admin_action',
        'admin/lang/add' => 'createdit_action',
        'admin/lang/edit/([a-z]+)' => 'createdit_action',
        'admin/lang/delete/([a-z]+)' => 'delete_action',
        'admin/lang/reindex' => 'reindex_action',
        'admin/lang/index' => 'index_action',
        'admin/lang/index/(\w+)' => 'index_action',
        'admin/lang/translate' => 'translate_action',
        'admin/lang/translate' => 'translate_action',
        'admin/lang/translate/([//\w_-]+)' => 'translate_action',
        'admin/lang/use/([//\w_-]+)' => 'save_action',
        'admin/lang/download/([//\w_-]+)' => 'download_action',
        'admin/lang/scan(.*?)' => 'scan_action',
        'admin/lang/ajax/(\w+)' => 'ajax_action',
        'admin/lang/ajax/(\w+)/(.+)' => 'ajax_action',
    );
    protected $access = array(
        '*' => array(1),
        'scan' => array(1),
        'translate' => array(1),
        'reindex' => array(1),
        'save' => array(1),
        'download' => array(1),
        'createdit' => array(1),
        'delete' => array(1),
    );

    const EXT = '.php';

    /**
     * Хук done
     */
    public function hookDone() {
        $this->response->header('charset', 'Content-Type: text/html; charset=utf-8');
    }

    /**
     * Хук на инициализацию шестеренки
     *
     * @param type $Gear
     */
    /*
      public function hookGearInit($Gear){
      $file = $Gear->dir.DS.'lang'.DS.$this->lang.self::EXT;
      if(is_dir(dirname($file)) && file_exists($file)){
      if($data = Config::read($file)){
      $this->import($data,$this->prepareSection($Gear->gear));
      }
      }
      }
     */

    /**
     * Конструктор
     */
    public function __construct($config) {
        $this->lang = config('lang.lang', 'ru');
        $this->locale = config('lang.locale') . '.UTF-8';
        $options = config('lang');
        $this->object(Lang::factory('index', $options));
        $this->object->load();
        setlocale(LC_ALL, $this->locale);
        parent::__construct($config);
    }

    /**
     * Хук на включение шестерёнки
     *
     * @param Gear $Gear
     * @param Core_ArrayObject $result
     */
    public function hookGearEnable($Gear, $result) {
        if ($result->success) {
            $install_lang = $Gear->getDir() . DS . 'lang' . DS . $this->lang . EXT;
            if (file_exists($install_lang)) {
                $lang = new Config($install_lang);
                Lang::factory('index')->import($lang->toArray())->save();
            }
        }
    }

    /**
     * Вывод меню в админке
     */
    public function hookAdminMenu($type = 'primary') {
        switch ($type) {
            case 1:
            case 'primary':
                new Menu_Tabs(array(
                            'name' => 'admin.lang',
                            'title' => TRUE,
                            'elements' => array(
                                array(
                                    'label' => icon('wrench') . ' ' . t('Общие'),
                                    'link' => l('/admin/lang')
                                ),
                                array(
                                    'label' => icon('globe') . ' ' . t('Перевод'),
                                    'link' => l('/admin/lang/translate'),
                                    'active' => in_array($this->router->getSegments(3), array('scan', 'translate', 'reindex', 'save'))
                                ),
                                array(
                                    'label' => icon('search') . ' ' . t('Сканирование'),
                                    'link' => l('/admin/lang/scan')
                                ),
                                array(
                                    'label' => icon('list') . ' ' . t('Индекс'),
                                    'link' => l('/admin/lang/index'),
                                    'class' => 'fl_r'
                                ),
                            ),
                        ));
                break;
            case 2:
            case 'secondary':
                new Menu_Pills(array(
                            'name' => 'admin.lang.settings',
                            'title' => TRUE,
                            'elements' => array(
                                array(
                                    'label' => icon('list') . ' ' . t('Список'),
                                    'link' => l('/admin/lang/')
                                ),
                                array(
                                    'label' => icon('plus') . ' ' . t('Добавить язык'),
                                    'link' => l('/admin/lang/add/'),
                                    'class' => 'fl_r'
                                ),
                            ),
                        ));
                break;
            case 3:
                new Menu_Pills(array(
                            'name' => 'admin.lang.index',
                            'title' => TRUE,
                            'elements' => array(
                                array(
                                    'label' => icon('upload') . ' ' . t('Импорт'),
                                    'link' => l('/admin/lang/index')
                                ),
                                array(
                                    'label' => icon('download-alt') . ' ' . t('Экспорт'),
                                    'link' => l('/admin/lang/index/export'),
                                ),
                                array(
                                    'label' => icon('refresh') . ' ' . t('Перестроить'),
                                    'link' => l('/admin/lang/reindex/'),
                                    'class' => 'fl_r'
                                ),
                            ),
                        ));
                break;
        }
    }

    /**
     * Menu
     *
     * @param string $name
     * @param object $menu
     */
    public function hookMenu($name, $menu) {
        switch ($name) {
            case 'admin':
                $menu->add(array(
                    'link' => l('/admin/lang'),
                    'label' => icon('comment') . ' ' . t('Язык'),
                    'order' => 200,
                ));
                break;
        }
    }

    /**
     * Transliteration
     *
     * @param   string  $text
     * @return   string
     */
    public function transliterate($text) {
        $data = new Core_ArrayObject();
        $data->text = $text;
        event('lang.transliterate', $data);
        return $data->text;
    }

    /**
     * Перевод строки
     *
     * @param string $text
     * @return string
     */
    public function translate($text) {
        if ($data = Lang::factory('index')->$text) {
            $text = $data;
        }
        if (func_num_args() > 1) {
            $args = func_get_args();
            $args = array_slice($args, 1);
            return $this->plural($text, $args);
        }
        return $text;
    }

    /**
     * Определение формы строки с множественными числами
     *
     * @param string $result
     * @param array $args
     * @return string
     */
    public function plural($result, $args) {
        // Find all (one|some|many)  for creating correct plural forms
        preg_match_all('#\((.+)\)#imU', $result, $matches);
        if (sizeof($matches[0]) > 0) {
            foreach ($matches[0] as $key => $val) {
                if (count(explode('|', $matches[1][$key])) > 1)
                    $result = str_replace($val, declOfNum($args[$key], $matches[1][$key]), $result);
            }
        }
        array_unshift($args, $result);
        return call_user_func_array('sprintf', $args);
    }

    /**
     * Редактирование основных настроек
     */
    public function admin_action() {
        $this->hookAdminMenu();
        $this->hookAdminMenu(2);
        template('Lang/templates/list', array('langs' => $this->getLangs(config('lang.available'))))->show();
//        $form = new Form('Lang/forms/admin');
//        $langs = $this->getLangs((array)config('lang.available'));
//        $form->lang->setValues($langs);
//        if ($data = $form->result()) {
//            $data->lang && $this->set('lang.lang', $data->lang);
//            flash_success(t('Настрйоки успешно сохранены!'));
//            redirect('/admin/lang');
//        }
//        $form->show();
    }

    /**
     * Возвращает список языков или расширенную информацию о языках по укзанным кодам
     *
     * @param array $filter
     * @return array
     */
    private function getLangs($filter = array()) {
        $all = new Config($this->dir . DS . 'languages' . EXT);
        $langs = array();
        if ($filter) {
            foreach ($filter as $lang) {
                if ($info = $all->$lang) {
                    $langs[$lang] = isset($info[1]) ? $info[1] : $info[0];
                }
            }
        } else {
            foreach ($all as $code => $info) {
                $langs[$code] = isset($info[1]) ? $info[1] : $info[0];
            }
        }
        return $langs;
    }

    /**
     * Добавление или редактирования языка
     */
    public function createdit_action() {
        $this->hookAdminMenu();
        $this->hookAdminMenu(2);
        $form = new Form(array(
                    'name' => 'lang.createdit',
                    'elements' => array(
                        'lang' => array(
                            'type' => 'select',
                            'label' => t('Выберите язык:'),
                            'values' => array(),
                        ),
                        'submit' => array(
                            'label' => t('Добавить'),
                        )
                    )
                ));
        $langs = $this->getLangs();
        $langs = array_diff_key($langs, array_fill_keys((array) config('lang.available'), ''));
        $form->lang->setValues($langs);
        if ($result = $form->result()) {
            $available = config('lang.available');
            $available->extend(array($result->lang));
            $this->set('lang.available', $available);
            flash_success(t('Новый язык добавлен!'));
            redirect('/admin/lang');
        }
        $form->show();
    }

    /**
     * Удаление языка
     *
     * @param string $lang
     */
    public function delete_action($lang) {
        if ($info = $this->getLangs(array($lang))) {
            if ($lang == config('lang.lang')) {
                flash_error(t('Вы не можете удалить текущий язык сайта!'));
                return back();
            }
            $available = config('lang.available');
            $new = array();
            foreach ($available as $value) {
                if ($value == $lang)
                    continue;
                $new[] = $value;
            }
            $info = reset($info);
            $this->config->set('lang.available', $new);
            $this->store();
            flash_success(t('Язык <b>«%s»</b> успешно удалён!', $info));
            back();
        }
    }

    /**
     * Перевод сайта на другой язык
     */
    public function translate_action($path = '') {
        $this->hookAdminMenu(1);
        $this->hookAdminMenu(2);
        $file = '';
        if ($path && $this->checkPath($path)) {
            $file = ROOT . DS . $path . $this->prepareFilePath();
        }
        template('Lang/templates/translate', array('path' => $path, 'file' => $file))->show();
    }

    /**
     * Сканирование файловой системы на предмет переводов
     */
    public function scan_action($path = '') {
        $this->hookAdminMenu(1);
        $this->hookAdminMenu(2);
        if (!$this->checkPath($path)) {
            return event('403');
        }
        template('Lang/templates/scan', array('path' => $path))->show();
    }

    /**
     * Пересобирает индекс из уже переведённых файлов шестерёнков и тем
     */
    public function reindex_action() {
        $gears_lang_files = File::findByMask(GEARS, '#[^a-z]' . $this->lang . '\.php$#');
        $themes_lang_files = File::findByMask(THEMES, '#[^a-z]' . $this->lang . '\.php$#');
        $lang_files = array_merge($gears_lang_files, $themes_lang_files);
        $index = Lang::factory('index');
        // Удаление предыдущего индекса
        unlink($index->getPath());
        foreach ($lang_files as $file) {
            $options = config('lang');
            $options->file = $file;
            $data = new Lang_Driver_File($options);
            $data->load();
            $index->import($data->export());
        }
        $index->save();
        flash_success(t('Индекс успешно пересобран!'));
        back();
    }

    /**
     *
     */
    public function index_action($action = 'import') {
        $this->hookAdminMenu(1);
        $this->hookAdminMenu(3);
        switch ($action) {
            case 'import':
                $form = new Form('Lang/forms/import');
                if ($result = $form->result()) {
                    if ($file = $result->file) {
                        $zip = new Zip(array(
                                    'file' => $file->path,
                                    'check' => array('type' => 'lang'),
                                ));

                        if ($zip->extract(LANG)) {
                            $info = $zip->info();
                            $langs = $this->getLangs(array($info['lang']));
                            success(t('<b>Архив успешно распакован!</b> Индекс для языка <b>«%s»</b> установлен.', implode($langs)),'','content');
                        }
                        $zip->close();
                        unlink($file->path);
                    }
                }
                $form->show();
                break;
            case 'export':
                template('Lang/templates/download')->show();
                break;
            case 'download':
                $file = ROOT.$this->prepareFilePath();
                $archive = TEMP.DS.  pathinfo($file,PATHINFO_FILENAME).'.zip';
                $zip = new Zip(array(
                   'file' => $archive,
                    'create' => TRUE,
                ));
                $zip->add($file);
                $zip->info(array(
                    'type' => 'lang',
                    'lang' => config('lang.lang'),
                ));
                $zip->close();
                File::download($archive, basename($archive),TRUE);
                break;
        }
    }

    /**
     * Проверка правильности пути, чтобы пользователи не сканировали лишнего
     *
     * @param type $path
     * @return boolean
     */
    private function checkPath($path) {
        if (!$path) {
            return TRUE;
        }
        if (!preg_match('#(gears|themes)/?([a-zA-Z0-9_]*)/?$#', $path, $matches)) {
            return FALSE;
        }
        return is_dir(ROOT . DS . $path);
    }

    /**
     * Обработка Ajax-запросов
     *
     * @param string $action
     */
    public function ajax_action($action = 'scan', $path = '') {
        if (!Ajax::is()) {
            return event('403');
        }
        $ajax = new Ajax();
        $ajax->success = TRUE;
        $ajax->text = '';
        switch ($action) {
            case 'change':
                if ($lang = $this->input->post('lang')) {
                    if ($this->getLangs(array($lang))) {
                        $this->set('lang.lang', $lang);
                        $this->config->store();
                    } else {
                        $ajax->success = FALSE;
                    }
                }
                break;
            case 'save':
                $path = $this->input->post('path');
                if ($path && $this->checkPath($path)) {
                    $file = ROOT . DS . $path . $this->prepareFilePath();
                    $options = config('lang');
                    $options->file = $file;
                    $index = Lang::factory('temp', $options);
                    $index->load();
                } else {
                    $index = Lang::factory('index');
                }
                if ($values = $this->input->post('values')) {
                    $index->import($values);
                } else {
                    $source = $this->input->post('source');
                    $translation = $this->input->post('translation');
                    $index->set($source, $translation);
                }
                $index->save();
                break;
            case 'scan':
                if (!$this->checkPath($path)) {
                    exit(t("Вы указали недопустимый путь!"));
                }
                // Важно! Если выбираем только шестерёнки или только темы,
                // то для каждой из них обновляются языковые файлы
                if (preg_match('#(gears|themes)/?$#', $path)) {
                    session('admin.lang.updateEach', TRUE);
                }
                $path = $path ? ROOT . DS . $path : ROOT;
                $index_file = $path . DS . LANG . DS . $this->lang . EXT;
                File::mkdir(dirname($index_file));
                if ($this->input->get('reset')) {
                    cogear()->session->remove('admin.lang.scan');
                    $ajax->action = 'reset';
                }
                // Сначала сканируем PHP-файлы
                $files = session('admin.lang.scan');
                if (!is_array($files)) {
                    $files = File::findByMask($path, '/^.+\.(php|js)$/i');
                    $files = array_reverse($files);
                    $folder = $path === ROOT ? '/' : File::pathToUri($path);
                    $ajax->text .= t('Сканирование папки <b>%s</b>…', $folder) . '<br/>';
                    session('admin.lang.scan.path', $path);
                    session('admin.lang.index', array());
                    session('admin.lang.scan', $files);
                    session('admin.lang.scan.counter', count($files));
                    session('admin.lang.scan.index', 1);
                    session('admin.lang.scan.translations', 1);
                    session('admin.lang.updateСurrent', FALSE);
                }
                if (!$files) {
                    $ajax->success = FALSE;
                    if (session('admin.lang.updateEach')) {
                        $this->dumpIndex($ajax);
                        cogear()->session->remove('admin.lang.updateEach');
                        cogear()->session->remove('admin.lang.updateCurrent');
                    } else {
                        $ajax->finish = TRUE;
                    }
                    $ajax->text .= t('<p class="alert alert-success">Сканирование завершено.</p>');
                    $ajax->result = t('Найдено <b>%d</b> строк', session('admin.lang.scan.translations'));
                    $ajax->text .= $ajax->result;
                    $ajax->index = session('admin.lang.scan.index');
                    $ajax->total = session('admin.lang.scan.counter');
                    cogear()->session->remove('admin.lang.scan');
                    cogear()->session->remove('admin.lang.scan.counter');
//                    cogear()->session->remove('admin.lang.scan.index');
                    cogear()->session->remove('admin.lang.scan.translations');
                    /**
                     *  $options = config('lang');
                      $options->path = dirname($index_file);
                      $index = Lang::factory('index', $options);
                      $index->load();
                      foreach ($strings as $string) {
                      $index->offsetExists($string) OR $index->set($string, '');
                      }
                      $index->save();
                     */
                } else {
                    $file = array_pop($files);
                    if (session('admin.lang.updateEach')) {
                        preg_match('#(gear|theme)s(?:\\\|/)([\w_-]+)#', $file, $matches);
                        if (!$current = session('admin.lang.updateСurrent')) {
                            session('admin.lang.updateСurrent', $current = array(
                                'type' => $matches[1],
                                'name' => $matches[2],
                                'file' => $file,
                            ));
                            switch ($current['type']) {
                                case 'gear':
                                    $ajax->text .= t('<p>Сканирую шестерёнку <b>«%s»</b>…<br/>', $current['name']);
                                    break;
                                case 'theme':
                                    $ajax->text .= t('<p>Сканирую тему <b>«%s»</b>…<br/>', $current['name']);
                                    break;
                            }
                        } else if ($current['name'] != $matches[2]) {
                            $this->dumpIndex($ajax, array(
                                'type' => $matches[1],
                                'name' => $matches[2],
                                'file' => $file,
                            ));
                            session('admin.lang.updateСurrent', $current = array(
                                'type' => $matches[1],
                                'name' => $matches[2],
                                'file' => $file,
                            ));
                        }
                    }
                    if ($strings = $this->parseFile($file)) {
                        if (is_array($strings)) {
                            $index = session('admin.lang.index');
                            $index = array_merge((array) $index, $strings);
                            session('admin.lang.index', array_unique($index));
                        }
                    }
                    $ajax->text .= t("Файл <b>%s</b> успешно обработан. ", File::pathToUri($file));
                    $ajax->result .= $strings ? t('Найдено: <b>%d (строка|строки|строк)</b> для перевода.', sizeof($strings)) : t('Строки для перевода не найдены.');
                    $ajax->text .= $ajax->result;
                    $ajax->result = icon('file') . ' <b>' . File::pathToUri($file) . '</b><br/>' . $ajax->result;
                    $ajax->index = session('admin.lang.scan.index');
                    $ajax->total = session('admin.lang.scan.counter');
                    $ajax->strings = session('admin.lang.scan.translations');
                    session('admin.lang.scan.index', session('admin.lang.scan.index') + 1);
                    $strings && session('admin.lang.scan.translations', session('admin.lang.scan.translations') + sizeof($strings));
                    session('admin.lang.scan', $files);
                }
                break;
        }
        $ajax->json();
    }

    /**
     * Сбрасывает индекс в текущую шестерёнкиу или тему
     */
    private function dumpIndex($ajax, $new = array()) {
        if ($current = session('admin.lang.updateСurrent')) {
            $path = substr($current['file'], 0, strpos($current['file'], $current['name']) + strlen($current['name']));
            $path .= $this->prepareFilePath();
            $this->updateLangFile($path, (array) session('admin.lang.index'));
            session('admin.lang.index', array());
            switch ($current['type']) {
                case 'gear':
                    $ajax->text .= t('Сохраняю результат обработки файлов шестерёнки <b>«%s»</b> в файл <i class="icon icon=file"></i> <b>%s</b>.</p>', $current['name'], File::pathToUri($path));
                    $new && $ajax->text .= t('<p>Сканирую шестерёнку <b>«%s»</b>…<br/>', $new['name']);
                    break;
                case 'theme':
                    $ajax->text .= t('Сохраняю результат обработки файлов темы <b>«%s»</b> в файл <i class="icon icon=file"></i> <b>%s</b>.</p>', $current['name'], File::pathToUri($path));
                    $new && $ajax->text .= t('<p>Сканирую тему <b>«%s»</b>…<br/>', $new['name']);
                    break;
            }
        }
    }

    /**
     * Если файла нет — создаёт его.
     *
     * Если файл есть — сверяется и обновляет значения.
     *
     * @param string $file
     * @param array $data
     */
    private function updateLangFile($path, $data) {
        // Если файл уже есть, то работаем с ним
        if (file_exists($path)) {
            // Загружаем его в драйвер
            $config = config('lang');
            $config->file = $path;
            $file = new Lang_Driver_File($config);
            $file->load();
            if ($file->count()) {
                // Теперь нужно удалить старые строки
                $original = $file->toArray();
                // Сравниваем
                if ($to_delete = array_diff_key($original, array_fill_keys($data, ''))) {
                    foreach ($to_delete as $key => $value) {
                        $file->offsetUnset($key);
                    }
                }
                // Обновляем информацию в индексе — добавляем новые строки
                foreach ($data as $value) {
                    if (!$file->$value) {
                        $file->set($value, '');
                    }
                }
                // Сохраняем
                $file->save();
                return;
            }
        }
        File::mkdir(dirname($path));
        file_put_contents($path, $this->prepareFile($data));
    }

    /**
     * Сохранение индекса
     *
     * @param type $path
     */
    public function save_action() {
        $this->hookAdminMenu(1);
        $this->hookAdminMenu(2);
        if ($index = session('admin.lang.index')) {
            if ($path = session('admin.lang.scan.path')) {
                $path .= $this->prepareFilePath();
                $this->updateLangFile($path, $index);
                flash_success(t('Индекс успешно записан в файл <b>%s</b>!', File::pathToUri($path)));
                flash_info(t('Индекс удалён.'));
                cogear()->session->remove('admin.lang.scan.path');
            } else {
                flash_error(t('Не указан путь файла, в который необходимо записать индекс.'));
            }
        } else {
            flash_error(t('Индекс пуст.'));
        }
        back();
    }

    /**
     * Выгрузка индекса в браузер
     *
     * @param type $path
     */
    public function download_action($path) {
        if ($path === 'index') {
            $file = $this->prepareFile((array) session('admin.lang.index'));
        } else {
            $file = ROOT . DS . $path . $this->prepareFilePath();
            if (!file_exists($file)) {
                return error(t('Файл <b>%s</b> не существует!', $file));
            }
        }
        File::download($file, $this->lang . EXT);
    }

    /**
     * Подготавливает содержимое файла
     *
     * @param type $index
     */
    public function prepareFile($index) {
        $data = array_fill_keys($index, '');
        ksort($data);
        return stripcslashes(PHP_FILE_PREFIX . "\n" . 'return ' . var_export($data, TRUE) . ';');
    }

    /**
     * Готовит путь для языкового файла
     *
     * @return string
     */
    public function prepareFilePath() {
        return DS . LANG . DS . $this->lang . EXT;
    }

    /**
     * Парсит файл на предмет строк перевода
     *
     * @param string $file
     */
    private function parseFile($file) {
        if (!file_exists($file))
            return;
        $contents = file_get_contents($file);
        preg_match_all('#[^\w+]t\([\'|"](.+?)[\'|"](\)|,)#S', $contents, $matches);
        return $matches[1] ? $matches[1] : FALSE;
    }

}

/**
 * Transliterate text to machine readable (simplty to latin chars)
 *
 * @param string $text
 * @return string
 */
function transliterate($text) {
    $cogear = getInstance();
    return $cogear->lang->transliterate($text);
}

/**
 * Plural forms for words
 *
 * @param       int $number number
 * @param       string $titles Array of words to make plural forms joined with |
 * @return      string
 * */
function declOfNum($number, $titles) {
    if ($number < 0)
        $number = -$number;

    $cases = array(2, 0, 1, 1, 1, 2);


    if (is_string($titles))
        $titles = explode('|', $titles);
    if (count($titles) < 3) {
        $titles = array_pad($titles, 3, end($titles));
    }
    $offset = ($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)];
    return isset($titles[$offset]) ? $titles[$offset] : array_shift($offset);
}

