<?php

/**
 * Taxonomy Term.
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Taxonomy
 * @subpackage
 */
class Taxonomy_Term extends Db_Tree {

    protected $table = 'taxonomy_terms';
    protected $primary = 'id';
    protected $template = 'Taxonomy.term';

    /**
     * Get blog Uri
     *
     * @return string
     */
    public function getLink($type = 'default') {
        switch ($type) {
            case 'edit':
                $uri = new Stack(array('name' => 'taxonomy.term.link.edit'));
                $uri->append('admin/taxonomy/term');
                $uri->append('edit');
                $uri->append($this->id);
                break;
            case 'delete':
                $uri = new Stack(array('name' => 'taxonomy.term.delete.link'));
                $uri->append('admin/taxonomy/term');
                $uri->append('delete');
                $uri->append($this->id);
                break;
            default:
                $uri = new Stack(array('name' => 'taxonomy.term.link'));
                $uri->append('taxonomy/terms');
                $uri->append($this->link);
        }
        return l('/' . $uri->render('/'));
    }

    /**
     * Create new term
     *
     * @param type $data
     */
    public function insert($data = NULL) {
        $data OR $data = $this->getData();
        $data['created_date'] = time();
        $this->aid OR $data['aid'] = user()->id;
        $data['ip'] = server('ip');
        if(!isset($data['link'])){
            $data['link'] = transliterate($data['name']);
        }
        if ($result = parent::insert($data)) {
            event('taxonomy.term.insert', $this,$data,$result);
        }
        return $result;
    }

    /**
     * Update term
     *
     * @param type $data
     */
    public function update($data = NULL) {
        $data OR $data = $this->getData();
        isset($data['body']) && $data['last_update'] = time();
        $data['ip'] = server('ip');
        if ($result = parent::update($data)) {
            event('taxonomy.term.update', $this, $data,$result);
        }
        return $result;
    }

    /**
     * Delete term
     */
    public function delete() {
        $uid = $this->aid;
        if ($result = parent::delete()) {
            event('taxonomy.term.delete',$this);
        }
        return $result;
    }

}