<?php

/**
 * Taxonomy gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Taxonomy_Gear extends Gear {

    protected $name = 'Taxonomy';
    protected $description = 'Provide taxonomy functionality.';
    protected $package = '';
    protected $order = 0;
    protected $hooks = array(
        'form.init.post' => 'hookFormPost',
        'form.result.post' => 'hookFormPostResult',
        'post.after' => 'hookPostAfter',
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
        if ($vocabularies = vocabularies()->findAll()) {
            $i = 1;
            $post = $Form->object();
            $config = array();
            foreach ($vocabularies as $vocabulary) {
                $name = 'taxonomy_' . $vocabulary->link;
                $link = new Taxonomy_Link();
                $link->pid = $post->id;
                $link->vid = $vocabulary->id;
                $links = $link->findAll();
                if ($vocabulary->is_open) {
                    $config['type'] = 'text';
                    $value = array();
                    if ($links) {
                        foreach ($links as $link) {
                            $value[] = term($link->tid)->name;
                        }
                        $config['value'] = implode(', ', $value);
                    }
                } else {
                    $config['type'] = 'select';
                    $config['values'] = $vocabulary->getTerms();
                    if ($vocabulary->is_multiple) {
                        $name .= '[]';
                        $config['multiple'] = 'multiple';
                    }
                    if ($links) {
                        $value = array();
                        foreach ($links as $link) {
                            $value[] = term($link->tid)->id;
                        }
                        $config['value'] = $value;
                    }
                }
                $config['label'] = $vocabulary->name;
                $config['order'] = '3.1.' . $i++;
                $config['class'] = 'taxonomy';
                $config['data-source'] = l('taxonomy/terms/' . $vocabulary->id);
                $Form->addElement($name, $config);
            }
        }
    }

    /**
     * Hook Form post result
     *
     * @param   object  $Form
     */
    public function hookFormPostResult($Form, $is_valid, $result) {
        if ($is_valid && $result) {
            $post = $Form->object();
            if ($vocabularies = vocabularies()->findAll()) {
                $i = 1;
                $config = array();
                $links_ids = array();
                foreach ($vocabularies as $vocabulary) {
                    $param = 'taxonomy_' . $vocabulary->link;
                    if (isset($result[$param])) {
                        $result[$vocabulary->link] = is_array($result[$param]) ? join(', ', $result[$param]) : trim(preg_replace('#[\s]+#', ' ', $result[$param]));
                        if ($vocabulary->is_open) {
                            $data = preg_split('#[\s]*[,][\s]*#', $result[$param]);
                            foreach ($data as $value) {
                                $term = term();
                                $term->vid = $vocabulary->id;
                                $term->name = $value;
                                if (!$term->find()) {
                                    $term->insert();
                                }
                                $link = new Taxonomy_Link();
                                $link->pid = $post->id;
                                $link->tid = $term->id;
                                $link->vid = $vocabulary->id;
                                if (!$link->find()) {
                                    $link->save();
                                }
                                $links_ids[] = $link->id;
                            }
                        } elseif (is_array($result[$param])) {
                            foreach ($result[$param] as $key) {
                                $link = new Taxonomy_Link();
                                $link->pid = $post->id;
                                $link->tid = $key;
                                $link->vid = $vocabulary->id;
                                if (!$link->find()) {
                                    $link->save();
                                }
                                $links_ids[] = $link->id;
                            }
                        }
                    }
                }
                if ($links_ids) {
                    $link = new Taxonomy_Link();
                    $link->pid = $post->id;
                    $link->where_not_in('id', $links_ids);
                    $link->delete();
                }
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
        $link = new Taxonomy_Link();
        $link->pid = $post->id;
        if ($links = $link->findAll()) {
            $vocabularies = array();
            foreach ($links as $link) {
                $vocabularies[$link->vid][] = $link->tid;
            }
            foreach ($vocabularies as $vid => $terms_ids) {
                if ($terms_ids) {
                    $vocabulary = vocabulary($vid);
                    $tpl = new Template('Taxonomy.post');
                    $tpl->vocabulary = $vocabulary;
                    $terms = terms();
                    $terms->where_in('id', $terms_ids);
                    $tpl->terms = $terms->findAll();
                    $Stack->append($tpl->render());
                }
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
            case 'admin':
                $menu->register(array(
                    'label' => icon('list-alt') . ' ' . t('Taxonomy'),
                    'link' => l('/admin/taxonomy'),
                    'order' => 150.1
                ));
                break;
        }
    }

    /**
     * Load terms for vocabulary via ajax
     *
     * @param type $vid
     */
    public function terms_action($vid) {
        if (Ajax::is() && user()->id && $vocabulary = vocabulary($vid)) {
            $this->db->like('name', $this->input->get('term'), 'after');
            Db_ORM::skipClear();
            if ($terms = $vocabulary->getTerms()) {
                $data = array();
                foreach ($terms as $key => $term) {
                    array_push($data, $term);
                }
                exit(json_encode($data));
            }
        }
        event('403');
    }

    /**
     * Control Panel
     *
     * @param type $type
     * @param type $action
     * @param type $id
     */
    public function admin($type = 'vocabulary', $action = 'list', $id = NULL) {
        $this->bc = breadcrumb();
        $this->bc->register(array(
            'label' => t('Taxonomy'),
            'link' => l('admin/taxonomy'),
        ));
        switch ($type) {
            case 'term':
                if ($id && $term = term($id)) {
                    $vocabulary = vocabulary($term->vid);
                    $this->bc->register(array(
                        'label' => $vocabulary->name,
                        'link' => $vocabulary->getLink('edit'),
                    ));
                    $this->bc->register(array(
                        'label' => t('Terms', 'Taxonomy'),
                        'link' => $vocabulary->getLink('edit.terms'),
                    ));
                    $this->bc->register(array(
                        'label' => $term->name,
                    ));
                    switch ($action) {
                        case 'edit':
                            $this->admin_term_edit($term);
                            break;
                    }
                    break;
                } else {
                    event('404');
                }
                break;
            case 'terms':
                if ($id && $vocabulary = vocabulary($id)) {
                    new Menu_Tabs(array(
                                'name' => 'taxonomy.terms.list',
                                'elements' => array(
                                    'list' => array(
                                        'label' => t('List'),
                                        'link' => l('admin/taxonomy/terms/list/' . $id),
                                    ),
                                    'add' => array(
                                        'label' => t('Create'),
                                        'link' => l('admin/taxonomy/terms/create/' . $id),
                                        'class' => 'fl_r',
                                    ),
                                    'edit' => array(
                                        'label' => t('Edit'),
                                        'link' => l($this->router->getUri()),
                                        'class' => 'fl_r',
                                        'access' => check_route('admin/taxonomy/terms/edit', Router::STARTS),
                                    ),
                                )
                            ));
                    $this->bc->register(array(
                        'label' => $vocabulary->name,
                        'link' => $vocabulary->getLink('edit'),
                    ));
                    $this->bc->register(array(
                        'label' => t('Terms', 'Taxonomy'),
                        'link' => $vocabulary->getLink('edit.terms'),
                    ));
                    switch ($action) {
                        case 'list':
                            $this->admin_terms_list($id);
                            break;
                        case 'create':
                            $this->admin_terms_create($id);
                            break;
                    }
                    break;
                } else {
                    $this->bc->register(array(
                        'label' => t('Not found'),
                    ));
                    event('404');
                }
                break;
            case 'vocabulary':
            default:
                new Menu_Tabs(array(
                            'name' => 'taxonomy.vocabulary.list',
                            'elements' => array(
                                'list' => array(
                                    'label' => t('List'),
                                    'link' => l('admin/taxonomy'),
                                ),
                                'add' => array(
                                    'label' => t('Create'),
                                    'link' => l('admin/taxonomy/vocabulary/create'),
                                    'class' => 'fl_r',
                                ),
                                'edit' => array(
                                    'label' => t('Edit'),
                                    'link' => l($this->router->getUri()),
                                    'class' => 'fl_r',
                                    'access' => check_route('admin/taxonomy/vocabulary/edit', Router::STARTS),
                                ),
                            )
                        ));
                switch ($action) {
                    case 'list':
                        $this->admin_vocabulary_list();
                        break;
                    case 'create':
                        $this->admin_vocabulary_create();
                        break;
                    case 'edit':
                        $this->admin_vocabulary_edit($id);
                        break;
                }
        }
    }

    /**
     * Show terms list for vocabulary
     */
    private function admin_terms_list($vid) {
        $list = new Taxonomy_List_Terms(array(
                    'where' => array('vid' => $vid),
                    'order' => array('name' => 'asc'),
                ));
    }

    /**
     * Create term
     *
     * @param type $vid
     */
    private function admin_terms_create($vid) {
        $this->bc->register(array(
            'label' => t('Create'),
        ));
        $form = new Form('Taxonomy.term');
        $form->options->elements->vid->label = '';
        $form->options->elements->vid->type = 'hidden';
        $form->options->elements->vid->value = $vid;
        $form->init();
        $form->elements->offsetUnset('delete');
        if ($result = $form->result()) {
            $term = term();
            $term->object()->adopt($result);
            if ($term->insert()) {
                flash_success(t('Term has been created!', 'Taxonomy'), NULL, 'growl');
                redirect(vocabulary($vid)->getLink('edit.terms'));
            }
        }
        $form->show();
    }

    /**
     * Edit term
     *
     * @param Taxonomy_Term $term
     */
    private function admin_term_edit(Taxonomy_Term $term) {
        $form = new Form('Taxonomy.term');
        $form->object($term);
        if ($result = $form->result()) {
            if ($result->delete && $term->delete()) {
                flash_success(t('Term has been deleted!', 'Taxonomy'));
            } else {
                $term->object()->adopt($result);
                if ($term->update()) {
                    flash_success(t('Term has been updated!', 'Taxonomy'));
                }
            }
            redirect(vocabulary($term->vid)->getLink('edit.terms'));
        }
        $form->show();
    }

    /**
     * Show list of vocabularies
     */
    private function admin_vocabulary_list() {
        $list = new Taxonomy_List_Vocabularies();
    }

    /**
     * Create vocabulary
     */
    private function admin_vocabulary_create() {
        $form = new Form('Taxonomy.vocabulary');
        $this->bc->register(array(
            'label' => t('Create'),
            'link' => l('admin/taxonomy/create'),
        ));
        $form->init();
        $form->elements->offsetUnset('delete');
        if ($result = $form->result()) {
            $vocabulary = vocabulary();
            $vocabulary->object()->adopt($result);
            if ($vocabulary->insert()) {
                flash_success(t('The vocabulary has been saved!', 'Taxonomy'), NULL, 'growl');
                redirect('/admin/taxonomy');
            }
        }
        $form->show();
    }

    /**
     * Edit the vocabulary
     *
     * @param type $id
     */
    private function admin_vocabulary_edit($id) {
        if ($vocabulary = vocabulary($id)) {
            $this->bc->register(array(
                'label' => $vocabulary->name,
                'link' => l('admin/taxonomy/edit/' . $id),
            ));
            $form = new Form('Taxonomy.vocabulary');
            $form->object($vocabulary);
            if ($result = $form->result()) {
                if ($result->delete && $vocabulary->delete()) {
                    flash_success(t('The vocabulary has been deleted!', 'Taxonomy'), NULL, 'growl');
                    return redirect('/admin/taxonomy');
                }
                $vocabulary->object()->extend($result);
                if ($vocabulary->update()) {
                    flash_success(t('The vocabulary has been updated!', 'Taxonomy'), NULL, 'growl');
                    redirect('/admin/taxonomy');
                }
            }
            $form->show();
        } else {
            event('404');
        }
    }

    /**
     *
     */
    public function getVocabularies() {
        $vocabularies = vocabularies();
        $vocabularies->order('name', 'ASC');
        $data = array();
        if ($result = $vocabularies->findAll()) {
            foreach ($result as $item) {
                $data[$item->id] = $item->name;
            }
        }
        return $data;
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

/**
 * Shortcut for vocabularies
 *
 * @param int $id
 * @param string    $param
 */
function vocabularies($id = NULL, $param = 'id') {
    if ($id) {
        $vocabularies = new Taxonomy_Vocabulary();
        $vocabularies->$param = $id;
        if ($result = $vocabularies->findAll()) {
            return $result;
        } else {
            return FALSE;
        }
    }
    return new Taxonomy_Vocabulary();
}

/**
 * Shortcut for vocabulary
 *
 * @param int $id
 * @param string    $param
 */
function vocabulary($id = NULL, $param = 'id') {
    if ($id) {
        $vocabulary = new Taxonomy_Vocabulary();
        $vocabulary->$param = $id;
        if ($vocabulary->find()) {
            return $vocabulary;
        } else {
            return FALSE;
        }
    }
    return new Taxonomy_Vocabulary();
}

/**
 * Shortcut for terms
 *
 * @param int $id
 * @param string    $param
 */
function terms($id = NULL, $param = 'id') {
    if ($id) {
        $terms = new Taxonomy_Term();
        $terms->$param = $id;
        if ($result = $terms->findAll()) {
            return $result;
        } else {
            return FALSE;
        }
    }
    return new Taxonomy_Term();
}

/**
 * Shortcut for term
 *
 * @param int $id
 * @param string    $param
 */
function term($id = NULL, $param = 'id') {
    if ($id) {
        $term = new Taxonomy_Term();
        $term->$param = $id;
        if ($term->find()) {
            return $term;
        } else {
            return FALSE;
        }
    }
    return new Taxonomy_Term();
}