<?php

/**
 *
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 */
class XmlConfig extends Object {

    /**
     * Конструктор
     */
    public function __construct($file) {
        $data = file_get_contents($file);
        $this->object(new SimpleXMLElement($data));
    }

    /**
     * Приводим элемент к массиву
     */
    public function parse($defaults = array()) {
        $defaults OR $defaults = (array) Gears::getDefaultSettings();
        $result = new Core_ArrayObject();
        $result['gear'] =  (string)$this->attributes()->name;
        $data = new Core_ArrayObject($defaults);
        // Накладываем поверх настройки из конфигурации шестеренки
        $filtered = $this->xpath('(/gear//*[@lang=\'' . config('i18n.lang') . '\'] | //*[not(@lang)])');
        $data->extend((array) $filtered[0]);
        foreach ($data as $name=>$tag) {
            // Убираем атрибуты из конечного результата
            if($name[0] == '@'){
                continue;
            }
            switch ($name) {
                case 'gear':
                case 'theme':
                    continue;
                    break;
                case 'require':
                case 'required':
                    $object = new Core_ArrayObject();
                    foreach ($tag as $cur_tag => $element) {
                        $object->$cur_tag OR $object->$cur_tag = new Core_ArrayObject();
                        $attributes = new Core_ArrayObject();
                        foreach ($element->attributes() as $key => $attribute) {
                            $attributes->offsetSet($key, $attribute->__toString());
                        }
                        $object->$cur_tag->append($attributes);
                    }
                    $result['required'] = $object;
                    break;
                default:
                    $result[$name] = (string) $tag;
            }
        }
        return $result;
    }

    /**
     * Адоптирование конфига из XML-файла
     *
     * @param   SimpleXMLElement    $xml
     * @param   array   $defaults
     */
    public function adoptXmlConfig(SimpleXMLElement $xml, $defaults = NULL) {
        // Временная заглушка от неизвестного бага
        if (FALSE == $xml instanceof SimpleXMLElement) {
            return;
        }

        // Установка настроек по умолчанию, если не переданого иного
        // Загружаем настройки по умолчанию
        // Присавиваем значения из настроек самой шестерёнке
        foreach ($gear as $key => $value) {
            property_exists($this, $key) && $this->$key = $value;
        }
//        if ($this->required instanceof SimpleXMLElement) {
//            $required = new Core_ArrayObject();
//            foreach ($this->required as $tag => $element) {
//                $required->$tag OR $required->$tag = new Core_ArrayObject();
//                $attributes = new Core_ArrayObject();
//                foreach ($element->attributes() as $key => $attribute) {
//                    $attributes->offsetSet($key, $attribute->__toString());
//                }
//                $required->$tag->append($attributes);
//            }
//            $this->required = $required;
//        }
        $attribute = $xml->xpath('@name');
        $this->gear = $attribute[0]->__toString();
    }

}