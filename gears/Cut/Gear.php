<?php

/**
 * Cut gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Cut_Gear extends Gear {

    protected $name = 'Cut';
    protected $description = 'Provite post cut functionality.';
    protected $package = '';
    protected $order = -100;
    protected $hooks = array(
        'markitup.toolbar' => 'hookMarkItUp',
        'post.render' => 'hookPostRender',
        'jevix' => 'hookJevix',
    );
    protected $routes = array(
    );
    protected $access = array(
    );

    /**
     * Acccess
     *
     * @param string $rule
     * @param object $Item
     */
    public function access($rule, $Item = NULL) {
        switch ($rule) {
            case 'create':
                return TRUE;
                break;
        }
        return FALSE;
    }

    public function hookJevix($jevix) {
        $jevix->cfgAllowTags(array('cut'));
        $jevix->cfgSetTagShort(array('cut'));
        $jevix->cfgAllowTagParams('cut', array('text'=>'#text'));
    }

    /**
     * Extend MarkItUp toolbar
     *
     * @param type $toolbar
     */
    public function hookMarkItUp($toolbar) {
        if (role() && $this->router->getSegments(0) == 'post') {
            $toolbar->markupSet->append(array(
                'name' => t('Cut'),
                'key' => 'Q',
                'className' => 'markItUpCut',
                'replaceWith' => '<cut text="[![Cut]!]"/>',
                'order' => 20,
            ));
        }
    }

    /**
     * Hook render post
     *
     * @param Post $Post
     */
    public function hookPostRender($Post) {
        if (preg_match_all('#(.*?)[\[\<]cut((\s*text)?=([^\]\>]+))?\s*/?[\]\>](.*?)#imsU', $Post->body, $matches)) {
            if($Post->teaser){
                $Post->body = $matches[1][0];
                if(!empty($matches[4][0])){
                    $Post->body .= template('Cut/templates/cut',array('text'=>$matches[4][0],'post'=>$Post))->render();
                }
            }
            else {
                $Post->body = $matches[1][0].'<div id="cut"></div>'.$matches[5][0];
            }
        }
    }

    /**
     * Init
     */
    public function init() {
        parent::init();
    }

    /**
     * Hook menu
     *
     * @param string $name
     * @param object $menu
     */
    public function menu($name, $menu) {
        switch ($name) {

        }
    }

    /**
     * Default dispatcher
     *
     * @param string $action
     * @param string $subaction
     */
    public function index_action($action = '', $subaction = NULL) {

    }

    /**
     * Custom dispatcher
     *
     * @param   string  $subaction
     */
    public function some_action($subaction = NULL) {

    }

}