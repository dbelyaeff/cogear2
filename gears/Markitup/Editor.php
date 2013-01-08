<?php

/**
 *  Markitup editor
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage

 */
class Markitup_Editor extends Wysiwyg_Abstract {

    /**
     * Load scripts
     */
    public function load() {
        $folder = cogear()->markitup->folder . '/';
        css($folder . 'skins/simple/style.css');
        css($folder . 'sets/default/style.css');
        js($folder . 'js/jquery.markitup.js', 'after');
        $toolbar = Core_ArrayObject::transform($toolbar = array(
                    'nameSpace' => 'html',
                    'onCtrlEnter' => array(
                        'keepDefault' => FALSE,
                        'openWith' => "\n<p>",
                        'closeWith' => "</p>\n",
                    ),
                    'onTab' => array(
                        'keepDefault' => false,
                        'openWith' => '  ',
                    ),
                    'markupSet' => array(
                        array(
                            'name' => t('Bold'),
                            'key' => 'B',
                            'openWith' => '<b>',
                            'closeWith' => '</b>',
                            'className' => 'markItUpBold',
                        ),
                        array(
                            'name' => t('Italic'),
                            'key' => 'I',
                            'openWith' => '<i>',
                            'closeWith' => '</i>',
                            'className' => 'markItUpItalic',
                        ),
                        array(
                            'name' => t('Underlined'),
                            'key' => 'U',
                            'openWith' => '<u>',
                            'closeWith' => '</u>',
                            'className' => 'markItUpUndeline',
                        ),
                        array(
                            'name' => t('Strike through'),
                            'key' => 'S',
                            'openWith' => '<s>',
                            'closeWith' => '</s>',
                            'className' => 'markItUpStrike',
                        ),
                        array(
                            'name' => t('Heading 1'),
                            'key' => '1',
                            'openWith' => '<h1>',
                            'closeWith' => '</h1>',
                            'className' => 'markItUpH1',
                        ),
                        array(
                            'name' => t('Heading 2'),
                            'key' => '2',
                            'openWith' => '<h2>',
                            'closeWith' => '</h2>',
                            'className' => 'markItUpH2',
                        ),
                        array(
                            'name' => t('Heading 3'),
                            'key' => '3',
                            'openWith' => '<h3>',
                            'closeWith' => '</h3>',
                            'className' => 'markItUpH3',
                        ),
                        array(
                            'name' => t('UL'),
                            'multiline' => true,
                            'openBlockWith' => "<ul>\n",
                            'closeBlockWith' => "\n</ul>\n",
                            'openWith' => " <li>",
                            'closeWith' => "</li>",
                            'className' => 'markItUpUl',
                        ),
                        array(
                            'name' => t('OL'),
                            'multiline' => true,
                            'openBlockWith' => "<ol>\n",
                            'closeBlockWith' => "\n</ol>\n",
                            'openWith' => " <li>",
                            'closeWith' => "</li>",
                            'className' => 'markItUpOl',
                        ),
                        array(
                            'name' => t('Picture'),
                            'key' => 'P',
                            'replaceWith' => '<img src="[![Source:!:http://]!]" alt="" />',
                            'className' => 'markItUpPicture',
                        ),
                        array(
                            'name' => t('Link'),
                            'key' => 'L',
                            'openWith' => '<a href="[![Link:!:http://]!]">',
                            'closeWith' => '</a>',
                            'className' => 'markItUpLink',
                        ),
                        array(
                            'name' => t('User'),
                            'key' => 'U',
                            'openWith' => '[user=[![User]!]]',
                            'className' => 'markItUpUser',
                        ),
                        array(
                            'name' => t('Code'),
                            'key' => 'O',
                            'openWith' => '<pre class="prettyprint linenums"><code>',
                            'closeWith' => '</code></pre>',
                            'className' => 'markItUpCode',
                        ),
                    )
                ));
        event('markitup.toolbar', $toolbar);
        inline_js("$('[name=$this->name]').markItUp(" . $toolbar->toJSON() . ")", 'after');
    }

}
