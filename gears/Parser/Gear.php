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
        'parse' => 'hookParse',
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
     * @param object $element
     * @param string $field
     */
    public function hookParse($element, $field = 'body') {
        $element->$field = $this->parse($element->$field);
    }

    /**
     * Парсер текста
     *
     * @param string $text
     */
    public function parse($text) {
        if (!self::$codes) {
            event('parser.codes', $this);
        }
        if (self::$codes) {
            foreach (self::$codes as $code => $callback) {
                $text = preg_replace_callback($code, $callback, $text);
            }
        }
        return $text;
    }

}