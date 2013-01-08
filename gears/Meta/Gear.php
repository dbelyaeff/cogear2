<?php

/**
 * Шестерёнка Мета
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Meta_Gear extends Gear implements SplObserver {

    public $info = array(
        'title' => array(),
        'keywords' => array(),
        'description' => array(),
    );
    protected $hooks = array(
        'post.full.after' => 'showObjectTitle',
        'blog.navbar.render' => 'showObjectTitle',
        'user.navbar.render' => 'showObjectTitle',
        'form.element.title.render' => 'showObjectTitle',
        'head' => 'hookHead',
        'response.send' => 'hookResponse',
    );

    const SNIPPET = '<!-- meta -->';
    const PREPEND = 0;
    const APPEND = 1;

    /**
     * Конструктор
     */
    public function __construct($config) {
        parent::__construct($config);
        $this->info = Core_ArrayObject::transform($this->info);
        $this->info->title->append(config('site.name', config('site.url')));
    }

    /**
     * Hook meta
     */
    public function hookResponse($Response) {
        $output = "";
        $output .= HTML::paired_tag('title', $this->info->title->toString(config('meta.title.delimiter', ' &raquo; '))) . "\n";
        $output .= HTML::tag('meta', array('type' => 'keywords', 'content' => $this->info->keywords->toString(', '))) . "\n";
        $output .= HTML::tag('meta', array('type' => 'description', 'content' => $this->info->description->toString('. '))) . "\n";
        $Response->output = str_replace(self::SNIPPET, $output, $Response->output);
    }

    /**
     * Generate <head> output
     */
    public function hookHead() {
        echo self::SNIPPET;
        event('meta');
    }
    /**
     * Add object title to meta
     *
     * @param object $object
     */
    public function showObjectTitle($object) {
        if ($object->label) {
            if(FALSE === $object->title){
                return;
            }
            title($object->label);
        } elseif ($object->getName()) {
            title($object->getName(FALSE));
        } elseif ($object->name) {
            title($object->name);
        } else if ($object->object && $object->object()->name) {
            title($object->object()->name);
        }
    }

    /**
     * Update title with menu changes
     */
    public function update(SplSubject $menu) {
        if (!$menu->options->title) {
            return;
        }
        foreach ($menu as $key => $item) {
            if($menu->options->titleActiveOnly && !$item->active) continue;
            if (FALSE !== $item->title) {
                title($item->title === TRUE ? $item->label : $item->title );
            }
        }
    }

}

function title($text, $position = NULL) {
    if ($text = trim(preg_replace('#\<.*?\>#imsU', '', strip_tags($text)))) {
        if ($position) {
            cogear()->meta->info->title->inject($text, $position);
        } else {
            cogear()->meta->info->title->prepend($text);
        }
    }
    return TRUE;
}

function keywords($text) {
    strpos($text, ',') && $text = explode(',', $text);
    if (is_array($text)) {
        foreach ($text as $value) {
            keywords(trim($value));
        }
        return;
    }
    $cogear = getInstance();
    $cogear->meta->info->title->append($text);
}

function description($text) {
    $cogear = getInstance();
    $cogear->meta->info->description->append($text);
}

function page_header($title, $level = 1) {
    append('info', '<div class="page-header"><h' . $level . '>' . $title . '</h' . $level . '></div>');
    title($title);
}