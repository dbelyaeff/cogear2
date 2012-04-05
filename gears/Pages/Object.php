<?php

/**
 *  Page object 
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Pages
 * @version		$Id$
 */
class Pages_Object extends Db_Item {
    const PATH_DELIM = '.';
    protected $template = 'Pages.page';

    /**
     * Constructor
     * 
     * @param   boolean $autoinit
     */
    public function __construct() {
        parent::__construct('pages');
    }

    /**
     * Save page
     */
    public function save() {
        // Event call
        event('page.save.before', $this);
        parent::save();
        // Generate hierarchy path
        $this->genPath();
        // Update object data
        $this->update();
        // Event call
        event('page.save.after', $this);
    }

    /**
     * Get url
     * 
     * @return  string
     */
    public function getUrl() {
        $link = str_replace(array('<id>', '<url>'), array($this->id, $this->url), config('pages.url', Pages_Gear::DEFAULT_PAGE_URL));
        $link = Url::link($link);
        return $link;
    }

}