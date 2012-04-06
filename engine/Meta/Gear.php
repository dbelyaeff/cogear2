<?php

/**
 * Meta gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Meta_Gear extends Gear {

    protected $name = 'Meta';
    protected $description = 'Meta information handler.';
    protected $order = -10;
   
    public $info = array(
        'title' => array(),
        'keywords' => array(),
        'description' => array(),
    );

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->info = Core_ArrayObject::transform($this->info);
    }
    /**
     * Init
     */
    public function init(){
        parent::init();
        title(t(config('site.name',config('site.url'))));
        hook('head',array($this,'head'),0);
        hook('menu.setActive',array($this,'menuTitleHook'));
        hook('Pages.showPage.before',array($this,'showObjectTitle'));
        hook('admin.gear.request',array($this,'showObjectTitle'));
    }
    /**
     * Add object title to meta
     * 
     * @param object $object 
     */
    public function showObjectTitle($object){
        $object->name && title(t($object->name));
    }
    /**
     * Set title from active menu element
     * 
     * @param string $element 
     */
    public function menuTitleHook($element){
        $this->info->title->inject(trim(strip_tags($element->value)),$this->info->title->count()-1);
    }
    /**
     * Generate <head> output
     */
    public function head(){
        echo HTML::paired_tag('title', $this->info->title->toString(config('meta.title.delimiter',' &raquo; ')))."\n";
        echo HTML::tag('meta', array('type'=>'keywords','content'=>$this->info->keywords->toString(', ')))."\n";
        echo HTML::tag('meta', array('type'=>'description','content'=>$this->info->description->toString('. ')))."\n";
        event('theme.head.meta.after');
    }       
}
function title($text) {
    $cogear = getInstance();
    $cogear->meta->info->title->prepend($text);
}
function keywords($text) {
    strpos($text,',') && $text = explode(',',$text);
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
