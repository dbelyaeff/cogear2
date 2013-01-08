<?php

/**
 * Шестеренка, управляющая правами доступа
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Access_Gear extends Gear {

    protected $rights;
    protected $hooks = array(
        'exit' => 'save',
        'router.exec' => 'hookRouterExec',
        '403' => 'hookAccessDenied'
    );

    /**
     * Хук Роутера
     *
     * @param object $Router
     * @param Callback $callback
     */
    public function hookRouterExec($Router, Callback $callback) {
        if (!access($callback->getCallback(0)->gear . '.*') && !access($callback->getCallback(0)->gear . '.' . str_replace('_action', '', $callback->getCallback(1)))) {
            event('403');
            return FALSE;
        }
    }

    /**
     * Show access denied page
     */
    public function hookAccessDenied() {
        flash('event.404', FALSE);
        $this->request();
        $tpl = new Template('Access/templates/denied');
        $tpl->show();
    }

    /**
     * Конструктор
     */
    public function __construct($config) {
        parent::__construct($config);
        $this->rights = new Core_ArrayObject();
    }

    /**
     * Initialize
     */
    public function init() {
        parent::init();
        foreach (cogear()->gears as $gear) {
            if (is_array($gear->access)) {
                foreach ($gear->access as $rule => $rights) {
                    $name = $gear->gear . '.' . $rule;
                    // Array of user roles
                    if (is_array($rights)) {
                        if (in_array(role(), $rights)) {
                            $this->rights->$name = TRUE;
                        } else {
                            $this->rights->$name = FALSE;
                        }
                    }
                    // If it's a callback
                    else if (is_string($rights)) {
                        $callback = new Callback(array($gear, $rights));
                        if ($callback->check()) {
                            $this->rights->$name = $callback;
                        }
                    } elseif (is_bool($rights)) {
                        $this->rights->$name = $rights;
                    }
                }
            }
        }
    }

    /**
     * Check rules
     *
     * @param string $rule
     * @return boolean
     */
    public function check($rule) {
        event('Access.check', $rule);
        if (flash('Access')) {
            flash('Access', FALSE);
            return TRUE;
        }
        if ($this->rights->$rule instanceof Callback) {
            $args = func_get_args();
            // Remove gear name from arg
            $args[0] = substr($args[0], strpos($args[0], '.') + 1);
            return $this->rights->$rule->run($args);
        } elseif (FALSE == $this->rights->$rule) {
            return FALSE;
        }
        return TRUE;
    }

}

/**
 * Shortcut for access rule check
 *
 * @param string $rule
 * @paramN …
 * @return boolean
 */
function access() {
    $args = func_get_args();
    $callback = new Callback(array(cogear()->access, 'check'));
    return $callback->run($args);
}