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
        'gear.dispatch' => 'hookGearAccess',
        'menu.auto.init' => 'hookMenuAuto',
        '403' => 'hookAccessDenied'
    );

    /**
     * Hook router
     *
     * @param object $Router
     */
    public function hookGearAccess($Gear, $args) {
        $gear = $Gear->gear;
        $args && $method = array_shift($args);
        if (!access($gear, $args)) {
            event('403');
            return FALSE;
        } elseif (!access($gear . '.' . $method, $args)) {
            event('403');
            return FALSE;
        }
    }

    /**
     * Hook menu auto
     *
     * @param object $Gear
     * @param object $Menu
     */
    public function hookMenuAuto($Gear, $Menu) {
        if (!access($Gear->gear)) {
            return FALSE;
        } elseif (!access($Gear->gear . '.menu')) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Show access denied page
     */
    public function hookAccessDenied() {
        $tpl = new Template('Access/templates/denied');
        $tpl->show();
    }

    /**
     * Конструктор
     */
    public function __construct($xml) {
        parent::__construct($xml);
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
                    // Set name for rule
                    // 'index' action is equal to gear name
                    // blog.index = blog
                    if ($rule == 'index') {
                        $name = $gear->gear;
                    } else {
                        $name = $gear->gear . '.' . $rule;
                    }
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
//        if ($this->user->id == 1) {
//            return TRUE;
//        }
        if ($this->rights->$rule instanceof Callback) {
            $args = func_get_args();
            // Remove gear name from arg
            $args[0] = substr($args[0], strpos($args[0], '.') + 1);
            return $this->rights->$rule->run($args);
        } elseif (FALSE === $this->rights->$rule) {
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