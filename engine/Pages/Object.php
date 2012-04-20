<?php

/**
 * Pages Object
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Pages_Object extends Db_Tree {

    protected $table = 'pages';
    protected $primary = 'id';
    protected $template = 'Pages.page';

    /**
     * Get page Uri
     *
     * @return string
     */
    public function getLink() {
        $uri = new Stack(array('name' => 'page.link'));
        $uri->append('page');
        $uri->append($this->id);
        return '/' . $uri->render('/');
    }
    /**
     * Get page Uri
     *
     * @return string
     */
    public function getEditLink() {
        $uri = new Stack(array('name' => 'page.edit.link'));
        $uri->append('admin');
        $uri->append('pages');
        $uri->append('edit');
        $uri->append($this->id);
        return '/' . $uri->render('/');
    }

    /**
     * Create new page
     *
     * @param type $data
     */
    public function insert($data = NULL) {
        $data OR $data = $this->object->toArray();
        $data['created_date'] = time();
        $data['last_update'] = time();
        $data['aid'] = cogear()->user->id;
        if ($result = parent::insert($data)) {

        }
        return $result;
    }

    /**
     * Update page
     *
     * @param type $data
     */
    public function update($data = NULL) {
        $data OR $data = $this->object->toArray();
        isset($data['body']) && $data['last_update'] = time();
        if ($result = parent::update($data)) {

        }
        return $result;
    }

    /**
     * Delete page
     */
    public function delete() {
        $uid = $this->aid;
        if ($result = parent::delete()) {

        }
        return $result;
    }

    /**
     * Render page
     */
    public function render($template = NULL) {
        event('page.render', $this);
        if (!$this->teaser) {
            $this->views++;
            $this->update(array('views' => $this->views));
        }
        return parent::render($template);
    }

}