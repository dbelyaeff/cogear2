<?php

/**
 * Blog Followers
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Blog_Followers extends Db_Item {

    protected $table = 'blogs_followers';

    /**
     * Insert blog reader
     *
     * @param type $data
     * @return
     */
    public function insert($data = NULL) {
        if ($result = parent::insert($data)) {
            event('blog.follower.insert', $this, $data, $result);
        }
        return $result;
    }

    /**
     * Update blog reader
     *
     * @param type $data
     * @return
     */
    public function update($data = NULL) {
        if ($result = parent::update($data)) {
            event('blog.follower.update', $this, $data, $result);
        }
        return $result;
    }

    /**
     * Delete blog reader
     *
     * @param type $data
     * @return
     */
    public function delete() {
        if ($result = parent::delete()) {
            event('blog.follower.delete', $this, array(), $result);
        }
        return $result;
    }

}