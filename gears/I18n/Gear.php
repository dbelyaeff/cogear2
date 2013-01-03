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
class I18n_Gear extends Gear {

    protected $domains = array();
    protected $lang;
    protected $locale;
    protected $hooks = array(
        'gear.init' => 'hookGearInit',
        'done' => 'hookDone',
        'menu' => 'hookMenu',
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
        $this->lang = config('i18n.lang', 'en');
        $this->locale = config('i18n.locale');
        $adapter = config('i18n.adapter', 'I18n_Adapter_File');
        $options = config('i18n');
        $this->object(new $adapter($options));
        setlocale(LC_ALL, $this->locale);
        date_default_timezone_set(config('i18n.timezone', 'Europe/Moscow'));
        parent::__construct($config);
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
                $menu->register(array(
                    'link' => l('/admin/i18n'),
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
        event('i18n.transliterate', $data);
        return $data->text;
    }

    /**
     * Translate
     *
     * @param string $text
     * @param string $domain
     * @return string
     */
    public function translate($text, $domain = '') {
        return $text;
//        return $this->get($text);
    }

    /**
     * Set or get domain
     *
     * @param   string  $domain If empty — return to previous domain
     * @return  string
     */
    public function domain($domain = '') {
        if ($domain) {
            array_push($this->domains, $domain);
        } else {
            return $this->domains ? array_pop($this->domains) : '';
        }
    }

    /**
     * Control Panel
     */
    public function admin_action($action = NULL) {
        switch ($action) {
            default:
                $form = new Form('I18n/forms/admin');
                if ($data = $form->result()) {
                    $data->lang && $this->set('i18n.lang', $data->lang);
                    success(t('Data is saved successfully!'));
                }
                $form->show();
        }
    }

}

/**
 * Simple translation
 *
 * @param   string  $text
 * @param   string  $domain
 * Optional params to parse via sprintf
 * @param   mixed   $param_1
 * …
 * @param   mixed   $param_N
 * @return  string
 */
function t($text) {
    $result = cogear()->i18n->translate($text);
    if (func_num_args() > 1) {
        $args = func_get_args();
        $args = array_slice($args, 1);
        // Find all (one|some|many)  for creating correct plural forms
        preg_match_all('#\((.+)\)#imU', $result, $matches);
        if (sizeof($matches[0]) > 0) {
            foreach ($matches[0] as $key => $val) {
                if (count(explode('|', $matches[1][$key])) > 1)
                    $result = str_replace($val, declOfNum($args[$key], $matches[1][$key]), $result);
            }
        }
        array_unshift($args, $result);
        $result = call_user_func_array('sprintf', $args);
    }
    return $result;
}

/**
 * Transliterate text to machine readable (simplty to latin chars)
 *
 * @param string $text
 * @return string
 */
function transliterate($text) {
    $cogear = getInstance();
    return $cogear->i18n->transliterate($text);
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
