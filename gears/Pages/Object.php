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
    public function getLink($type = 'default') {
        switch ($type) {
            case 'edit':
                $uri = new Stack(array('name' => 'page.link.edit'));
                $uri->append('admin');
                $uri->append('pages');
                $uri->append('edit');
                $uri->append($this->id);
                break;
            default:
                $uri = new Stack(array('name' => 'page.link'));
                if(!config('Pages.root_link',FALSE)){
                    $uri->append('pages');
                }
                $uri->append($this->link);
        }
        return l('/' . $uri->render('/'));
    }

    /**
     * Create new page
     *
     * @param type $data
     */
    public function insert($data = NULL) {
        $data OR $data = $this->object()->toArray();
        $data['created_date'] = time();
        $data['last_update'] = time();
        $data['aid'] = cogear()->user->id;
        if ($result = parent::insert($data)) {
            event('page.insert',$this,$data);
        }
        return $result;
    }

    /**
     * Update page
     *
     * @param type $data
     */
    public function update($data = NULL) {
        $data OR $data = $this->object()->toArray();
        isset($data['body']) && $data['last_update'] = time();
        if ($result = parent::update($data)) {
            event('page.update',$this,$data);
        }
        return $result;
    }

    /**
     * Delete page
     */
    public function delete() {
        $uid = $this->aid;
        if ($result = parent::delete()) {
            event('page.delete',$this);
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