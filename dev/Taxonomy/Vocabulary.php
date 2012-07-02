<?php

/**
 * Taxonomy Vocabulary.
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Taxonomy
 * @subpackage
 */
class Taxonomy_Vocabulary extends Db_Item {

    protected $table = 'taxonomy_vocabulary';
    protected $primary = 'id';
    const PRVT = 0;
    const PBLC = 1;
    const CLOSED = 0;
    const OPEN = 1;
    /**
     * Get blog Uri
     *
     * @return string
     */
    public function getLink($type = 'default') {
        switch ($type) {
            case 'edit':
                $uri = new Stack(array('name' => 'taxonomy.vocabulary.link.edit'));
                $uri->append('admin/taxonomy/vocabulary');
                $uri->append('edit');
                break;
            case 'edit.terms':
                $uri = new Stack(array('name' => 'taxonomy.terms.link.edit'));
                $uri->append('admin/taxonomy/terms/list');
                break;
            case 'delete':
                $uri = new Stack(array('name' => 'taxonomy.vocabulary.delete.link'));
                $uri->append('admin/taxonomy/vocabulary');
                $uri->append('delete');
                break;
            default:
                $uri = new Stack(array('name' => 'taxonomy.link'));
                $uri->append('taxonomy/vocabulary');
        }
        $uri->append($this->id);
        return l('/' . $uri->render('/'));
    }

    /**
     * Get terms
     *
     * @return array
     */
    public function getTerms(){
        $data = array();
        if($terms = terms($this->id,'vid')){
            foreach($terms as $term){
                $data[$term->id] = $term->name;
            }
        }
        return $data;
    }

    /**
     * Create new vocabulary
     *
     * @param type $data
     */
    public function insert($data = NULL) {
        $data OR $data = $this->getData();
        $data['created_date'] = time();
        $this->aid OR $data['aid'] = user()->id;
        $data['ip'] = server('ip');
        if ($result = parent::insert($data)) {
            event('taxonomy.vocabulary.insert', $this, $data, $result);
        }
        return $result;
    }

    /**
     * Update vocabulary
     *
     * @param type $data
     */
    public function update($data = NULL) {
        $data OR $data = $this->getData();
        isset($data['body']) && $data['last_update'] = time();
        $data['ip'] = server('ip');
        if ($result = parent::update($data)) {
            event('taxonomy.vocabulary.update', $this, $data, $result);
        }
        return $result;
    }

    /**
     * Delete vocabulary
     */
    public function delete() {
        $uid = $this->aid;
        $terms = terms($this->id, 'vid');
        if($terms){
            foreach($terms as $item){
                $item->delete();
            }
        }
        if ($result = parent::delete()) {
            event('taxonomy.vocabulary.delete', $this);
        }
        return $result;
    }

}