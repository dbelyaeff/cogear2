<?php

/**
 * Проверака на занятость Пути страницы
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2013, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Pages_Validate_Link extends Form_Validate_Abstract {

    /**
     * Validation
     *
     * @param	string	$value
     */
    public function validate($link) {
        $link = trim($link, '/');
        if ($route = route($link, 'route')) {
            die();
            if ($page = page($route->id, 'route')) {
                if ($page->id != $this->element->form->object()->id) {
                    return $this->element->error(t('Данный путь уже занят страницей <a href="%s">%s</a>', $page->getLink('edit'), $page->name));
                }
            }
        }
        return TRUE;
    }

}

