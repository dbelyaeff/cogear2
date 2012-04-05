<?php

/**
 * Pages gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Pages
 * @version		$Id$
 */
class Pages_Gear extends Gear {

    protected $name = 'Pages';
    protected $description = 'Manage pages.';
    protected $pages = array();
    protected $type = Gear::MODULE;
    protected $package = 'Pages';
    protected $order = 0;
    const DEFAULT_PAGE_URL = 'page/<id>';

    /**
     * Init
     */
    public function init() {
        parent::init();
        $cogear = getInstance();
        $cogear->router->addRoute(':index', array($this, 'index'), TRUE);
        $url = config('pages.url', Pages_Gear::DEFAULT_PAGE_URL);
        $cogear->router->addRoute(str_replace(array(
                    '.',
                    '<id>',
                    '<url>',
                        ), array(
                    '\.',
                    '(?P<id>\d+)',
                    '.+'
                        ), $url), array($this, 'catchPage'), TRUE);
        allow_role(array('pages create'), 100);
    }

    /**
     * Menu handler
     * 
     * @param object $menu 
     */
    public function menu($name, &$menu) {
        switch ($name) {
            case 'user':
                if ($this->user->id) {
                    $menu->{Url::gear('pages')} = t('My Pages', 'Pages');
                }
                break;
//            case 'admin':
//                $menu->{'pages'} = t('Pages');
//                break;
            case 'tabs_pages':
                if ($this->user->id) {
                    $menu->{'/'} = t('All');
                    $menu->{'create'} = t('Add new');
                    $menu->{'create'}->class = 'fl_r';
                }
                if ($this->router->getSegments(1) == 'edit') {
                    $menu->{'edit'} = t('Edit', 'Pages');
                }
                break;
        }
    }

    /**
     * Show pages
     * 
     * @param string $type 
     */
    public function index($action = '', $subaction = NULL) {
        new Menu_Tabs('pages', Url::gear('pages'));
        switch ($action) {
            case 'create':
                if (!page_access('pages create'))
                    return;
                $form = new Form('Pages.createdit');
                if ($result = $form->result()) {
                    $page = new Pages_Object();
                    $page->attach($result);
                    $page->aid = cogear()->user->id;
                    $page->created_date = time();
                    $page->last_update = time();
                    $page->save();
                    flash_success(t('New page has been successfully added!', 'Pages'));
                    redirect($page->getUrl());
                }
                append('content', $form->render());
                break;
            case 'show':
                $this->showPage($subaction);
                break;
            case 'edit':
                $page = new Pages_Object();
                $page->where('id', intval($subaction));
                if ($page->find()) {
                    if (access('pages edit_all') OR $cogear->user->id == $page->aid) {
                        $form = new Form('Pages.createdit');
                        $form->init();
                        if (access('pages delete')) {
                            $form->addElement('delete', array('label' => t('Delete'), 'type' => 'submit'));
                        }
                        $form->setValues($page->object);
                        if ($result = $form->result()) {
                            if ($result->delete) {
                                $page->delete();
                                redirect(Url::gear('pages'));
                            }
                            $page->object->mix($result);
                            $page->last_update = time();
                            $page->update();
                            $link = $page->getUrl();
                            success(t('Page has been update. You can visit it by link <a href="%s">%s</a>', 'Pages', $link, $link));
                            //redirect($page->getUrl());
                        }
                        $form->elements->submit->setValue(t('Update'));
                        append('content', $form->render());
                    } else {
                        return _403();
                    }
                } else {
                    return _404();
                }
                break;
            default:
                $this->showPages($action);
        }
    }

    /**
     * Show pages
     * 
     * @param int $page 
     */
    public function showPages($page) {
        $grid = new Grid('Pages.my');
        $pages = new Pages_Object();
        $this->db->order('id', 'DESC');
        $pager = new Pager_Pages(array(
            'count' => $pages->count(),
            'current' => $page,
            'per_page' => config('pages.per_page',2),
            'base_uri' => Url::gear('pages'),
            'order' => 1,
            'ajaxed' => TRUE,
            'target' => 'content'
        ));
        $grid->adopt($pages->findAll());
        $grid->show();
        $pager->show();     
    }

    /**
     * Catch page from specific route and render it
     */
    public function catchPage() {
        $cogear = getInstance();
        $matches = $cogear->router->getMatches();
        $this->showPage($matches['id']);
    }

    /**
     * Show page
     * 
     * @param   int $id
     */
    public function showPage($id) {
        $page = new Pages_Object();
        $page->where('id', $id);
        if ($page->find()) {
            event('Pages.showPage.before', $page);
            $this->renderPage($page);
            event('Pages.showPage.after', $page);
        } else {
            return _404();
        }
    }

    /**
     * Render page
     * 
     * @param   object  $page
     */
    public function renderPage($page) {
        $tpl = new Template('Pages.page');
        $tpl->item = $page;
        append('content', $tpl->render());
    }

}