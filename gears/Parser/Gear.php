<?php

/**
 * Шестеренка Parser
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Parser_Gear extends Gear {

    protected $hooks = array(
//        'parse' => 'hookParse',
    );
    public static $codes = array(
    );

    /**
     * Конструтктор
     *
     * @param type $config
     */
    public function __construct($config) {
        parent::__construct($config);
    }

    /**
     * Хук парсера
     *
     * @param object $item
     * @param string $field
     */
    public function hookParse($item, $field = 'body') {
        $item->$field = $this->parse($item->$field, $item);
    }

    /**
     * Парсер текста
     *
     * @param string $text
     */
    public function parse($text, $item) {
        if (self::$codes) {
            foreach (self::$codes as $code => $callback) {
                if (preg_match($code, $text, $matches)) {
                    $callback = new Callback($callback);
                    if ($callback->check()) {
                        $callback->setArgs(array($matches, $item));
                        if ($result = $callback->run()) {
                            $text = $result;
                        }
                    }
                }
            }
            return $text;
        }
    }

}