<?php

/**
 * Access control gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Access
 * @version		$Id$
 */
class Access_Gear extends Gear {

    protected $name = 'Access';
    protected $description = 'Access control gear';
    protected $rights;
    protected $refresh_flag;
    protected $order = -9;
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
        if (!access($gear,$args)) {
            return event('403');
        } elseif (!access($gear . '.' . $method,$args)) {
            return event('403');
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
            return TRUE;
        } elseif (!access($Gear->gear . '.menu')) {
            return TRUE;
        }
    }

    /**
     * Show access denied page
     */
    public function hookAccessDenied(){
        $tpl = new Template('Access.denied');
        $tpl->show();
    }

    /**
     * Initialize
     */
    public function init() {
        parent::init();
        $this->rights = new Core_ArrayObject();
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
                        if (in_array($this->user->role, $rights)) {
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
        if ($this->user->id == 1) {
            return TRUE;
        }
        if($this->rights->$rule instanceof Callback){
            $args = func_get_args();
            // Remove gear name from arg
            $args[0] = substr($args[0],strpos($args[0],'.')+1);
            return $this->rights->$rule->run($args);
        }
        elseif ($this->rights->$rule !== FALSE) {
            return TRUE;
        }
        return FALSE;
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
    $callback = new Callback(array(cogear()->access,'check'));
    return $callback->run($args);
}