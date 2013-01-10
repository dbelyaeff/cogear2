<?php

/**
 * Шестеренка для работы с программным кодом
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Code_Gear extends Gear {

    protected $hooks = array(
        'jevix' => 'hookJevix',
        'post.edit' => 'hookPostEdit',
        'menu.admin' => 'hookMenuAdmin',
        'form.element.editor.render' => 'hookFormEditorRender',
        'parse' => 'hookParse'
    );
    protected $routes = array(
        'admin/code' => 'admin_action',
        'admin/code/snippet' => 'snippet_action',
        'admin/code/snippet/(\d+)' => 'snippet_action',
    );
    protected $access = array(
        'admin' => array(1),
        'snippet' => array(1),
    );

    /**
     * Парсинг кода
     */
    public function hookParse($item) {
        if ($item->body && preg_match_all('#\[code\s+snippet=(\d+)\]#', $item->body, $matches)) {
            for ($i = 0; $i < sizeof($matches[0]); $i++) {
                foreach ($matches as $match) {
                    $snippet = new Code_Snippet();
                    $snippet->id = $matches[1][$i];
                    if ($snippet->find()) {
                        $item->body = str_replace($matches[0][$i], $snippet->render(), $item->body);
                    } else {
                        $item->body = str_replace($matches[0][$i], t('<i>Парсер не нашёл указазного сниппета в базе данных.</i>'), $item->body);
                    }
                }
            }
        }
    }

    /**
     * Хук для вывода кнопки под редактором
     *
     * @param type $Editor
     */
    public function hookFormEditorRender($Editor) {
        $Editor->options->after->append(template('Code/templates/hooks/editor')->render());
    }

    /**
     * Добавлени пункта в админ меню
     *
     * @param type $menu
     */
    public function hookMenuAdmin($menu) {
        $menu->add(array(
            'label' => icon('fire') . ' ' . t('Код'),
            'link' => l('/admin/code'),
            'order' => 555,
        ));
    }

    /**
     * Вывод админ меню
     */
    public function hookAdminMenu() {
        new Menu_Tabs(array(
                    'name' => 'code.admin',
                    'elements' => array(
                        array(
                            'label' => icon('list') . ' ' . t('Список'),
                            'link' => l('/admin/code')
                        ),
                        array(
                            'label' => icon('plus') . ' ' . t('Добавить'),
                            'link' => l('/admin/code/snippet'),
                            'class' => 'fl_r'
                        ),
                        array(
                            'label' => icon('pencil') . ' ' . t('Редактировать'),
                            'link' => l('/admin/code/snippet/' . $this->router->getSegments(3)),
                            'access' => check_route('admin/code/snippet/\d+'),
                            'class' => 'fl_r'
                        ),
                    )
                ));
    }

    /**
     * Хук парсера Jevix
     *
     * Добавляем автоматом тег, чтобы наш код подсвечивался
     *
     * @param object $Jevix
     */
    public function hookJevix($Jevix) {
        $Jevix->cfgSetTagParamDefault('pre', 'class', 'prettyprint', true);
    }

    /**
     * Для того, чтобы prettyprint не лез в редактор, нужно удалить класс у <pre>
     *
     * @param object $Post
     * @param object $Form
     */
    public function hookPostEdit($Post, $Form) {
        $Post->body = str_replace('prettyprint', '', $Post->body);
    }

    /**
     * Страница управления сниппетами
     */
    public function admin_action() {
        $this->hookAdminMenu();
        $snippet = new Code_Snippet();
        $snippet->order('id', 'DESC');
        if ($q = $this->input->get('q')) {
            $snippet->like('name', $q)->or_like('code', $q);
        }
        template('Search/templates/form', array('action' => ''))->show();
        $pager = new Pager(array(
                    'count' => $snippet->countAll(),
                    'per_page' => 20,
                    'base' => l('/admin/code'),
                ));
        if ($snippets = $snippet->findAll()) {
            $tpl = new Template('Code/templates/list');
            $tpl->snippets = $snippets;
            $tpl->show();
            $pager->show();
        } else {
            event('empty');
        }
    }

    /**
     * Добавление или редактирование сниппета
     *
     * @param int $id
     */
    public function snippet_action($id = NULL) {
        $this->hookAdminMenu();
        $form = new Form('Code/forms/snippet');
        $tpl = new Template('Code/templates/form');
        $snippet = new Code_Snippet();
        if ($id) {
            $snippet->id = $id;
            if ($snippet->find()) {
                $tpl->code = $snippet->code;
                $form->object($snippet);
            }
        } else {
            $tpl->code = htmlspecialchars('<?php

');
        }
        $snippet->name OR $form->remove('delete');
        $form->code_editor->options->label = $tpl->render();
        if ($result = $form->result()) {
            $snippet->name = $result->name;
            $snippet->code = $result->code;
            $snippet->aid OR $snippet->aid = user()->id;
            $snippet->type = $result->type;
            $snippet->created_date OR $snippet->created_date = time();
            if ($snippet->save()) {
                flash_success(t('Сниппет кода успешно сохранён!'));
                redirect(l('/admin/code/snippet/' . $snippet->id));
            }
        }
        $form->show();
    }

}