<?php

/**
 * Шестерека «Кат»
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Cut_Gear extends Gear {

    protected $hooks = array(
        'parse' => 'hookParse',
        'form.post.element.editor.render' => 'hookFormEditorRender'
    );

    /**
     * Хук для вывода кнопки под редактором
     *
     * @param type $Editor
     */
    public function hookFormEditorRender($Editor) {
        $Editor->options->after->append(template('Cut/templates/hooks/editor')->render());
    }

    /**
     * Обработка кода ката
     *
     * @param Post $Post
     */
    public function hookParse($item) {
        if (preg_match('#(.+)(\[cut(?:\s+text=([^\]]+?))?\])#imU', $item->body, $matches)) {

            if ($item->teaser) {
                $cut = template('Cut/templates/cut', array('item' => $item, 'text' => isset($matches[3]) ? $matches[3] : config('Cut.text', t('Читать далее…'))))->render();
                $item->body = $matches[1] . $cut;
            } else {
                $item->body = str_replace($matches[2], '<div id="cut"></div>', $item->body);
            }
        }
    }

}