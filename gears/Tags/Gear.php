<?php

/**
 * Tags gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Tags_Gear extends Gear {

    protected $name = 'Tags';
    protected $description = 'Manage tags';
    protected $package = '';
    protected $order = 0;
    protected $hooks = array(
        'form.init.post' => 'hookFormPost',
        'form.result.post' => 'hookFormPostResult',
        'post.after' => 'hookPostAfter',
        'widgets' => 'hookWidgets',
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

    /**
     * Hook form init
     *
     * @param Form $Form
     */
    public function hookFormPost($Form) {
        $config['label'] = t('Tags');
        $config['order'] = '3.1.2';
        $config['type'] = 'text';
        $config['class'] = 'autocomplete';
        $config['data-source'] = l('/tags/autocomplete/');
        $Form->addElement('tags', $config);
    }

    /**
     * Hook Form post result
     *
     * @param   object  $Form
     */
    public function hookFormPostResult($Form, $is_valid, $result) {
        if ($is_valid && $result) {
            $post = $Form->object();
            if ($result->tags) {
                $tags = preg_split('#[\s]*[,][\s]*#', trim($result->tags), NULL, PREG_SPLIT_NO_EMPTY);
                $link = new Tags_Link();
                $link->pid = $post->id;
                $link->delete();
                $tags = array_unique($tags);
                foreach ($tags as $value) {
                    if ($value && !$tag = tag($value)) {
                        $tag = tag();
                        $tag->name = $value;
                        $tag->insert();
                    }
                    $link = new Tags_Link();
                    $link->tid = $tag->id;
                    $link->pid = $post->id;
                    $link->insert();
                }
                $result->tags = implode(', ', $tags);
            }
        }
    }

    /**
     * Hook Post After
     *
     * @param type $Stack
     */
    public function hookPostAfter($Stack) {
        $post = $Stack->object();
        if ($tags = preg_split('#[\s]*[,][\s]*#', $post->tags, NULL, PREG_SPLIT_NO_EMPTY)) {
            $Stack->append(template('Tags/templates/post', array('tags' => $tags))->render());
        }
    }

    /**
     * Hook widgets
     *
     * @param Core_ArrayObject $widgets
     */
    public function hookWidgets($widgets){
        $widgets->append(new Tags_Widget());
    }
    /**
     * Default dispatcher
     *
     * @param string $action
     * @param string $subaction
     */
    public function index_action($tag = '') {
        if($tags = $this->input->get('tags')){
            $tags = preg_split('#([\s,]+)#', $tags, -1, PREG_SPLIT_NO_EMPTY);
            !$tag && $tag = $tags[0];
            $elements = array();
            foreach($tags as $t){
                $elements[] = array(
                    'label' => $t,
                    'link' => l('/tags/'.$t).e(),
                    'active' => $t == $tag,
                );
            }
            new Menu_Pills(array(
                'name' => 'tags',
                'elements' => $elements,
            ));
        }
        if ($tag && $tag = tag($tag)) {
            $bc = new Breadcrumb_Object(array(
                        'name' => 'tags',
                        'elements' => array(
                            array(
                                'label' => t('Tags', 'Tags'),
                                'link' => l('/tags/'),
                            ),
                            array(
                                'label' => $tag->name,
                            )
                        ),
                    ));
            page_header($tag->name);
            $link = new Tags_Link();
            $link->tid = $tag->id;
            if ($links = $link->findAll()) {
                $ids = array();
                foreach ($links as $link) {
                    $ids[] = $link->pid;
                }
                new Post_List(array(
                            'name' => 'user.posts',
                            'base' => l('/tags/' . $tag->name),
                            'per_page' => config('User.posts.per_page', 5),
                            'where_in' => array('id' => $ids),
                        ));
            } else {
                event('empty');
            }
        } else {
            page_header(t('Tags', 'Tags'));
            template('Tags/templates/search')->show();
            $tags_cloud = new Tags_Cloud();
            $tags_cloud->show();
        }
    }

    /**
     * Autocomplete
     */
    public function autocomplete_action() {
        if (Ajax::is() && user()->id && $query = $this->input->get('query')) {
            $tag = tag();
            $tag->like('name', $query, 'after');
            if ($result = $tag->findAll()) {
                $data = array('query' => $query, 'suggestions' => array());
                foreach ($result as $tag) {
                    $data['suggestions'][] = $tag->name;
                }
                sort($data['suggestions']);
                exit(json_encode($data));
            }
            exit();
        }
        return event('404');
    }

}

/**
 * Shortcut for tag
 *
 * @param int $id
 * @param string    $param
 */
function tag($value = NULL, $param = 'name') {
    if ($value !== NULL) {
        $tag = new Tags_Tag();
        $tag->$param = $value;
        if ($tag->find()) {
            return $tag;
        } else {
            return FALSE;
        }
    }
    return new Tags_Tag();
}