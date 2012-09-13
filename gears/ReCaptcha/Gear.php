<?php

/**
 * ReCaptcha gear
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class ReCaptcha_Gear extends Gear {

    protected $name = 'ReCaptcha';
    protected $description = 'ReCaptcha antibot system';
    protected $package = '';
    protected $order = 0;
    protected $hooks = array(
        'form.init.user-login' => 'hookLoginForm',
        'form.result.after' => 'hookFormResult',
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
     * Extend login form
     *
     * @param type $Form
     */
    public function hookLoginForm($Form) {
        if ($this->session->get('form.failed') > config('ReCaptcha.showOnFormFailedCount',3) && !Ajax::is()) {
            $Form->addElement('recaptcha', array(
                'type' => 'captcha',
                'label' => t('Security code', 'ReCaptcha'),
                'description' => t('You\'ve tried to submit this form for already %d times. <br/>In order to identify your humanity, enter the displayed code.', 'ReCaptcha',$this->session->get('form.failed')),
                'order' => 3
            ));
        }
    }
    /**
     * Calc form post failed
     *
     * @param object $Form
     * @param boolean $is_valid
     * @param array $result
     */
    public function hookFormResult($Form,$is_valid,$result){
        if(!$is_valid){
            if($count = $this->session->get('form.failed')){
                $count++;
            }
            else {
                $count = 1;
            }
            $this->session->set('form.failed',$count);
        }
        else {
            $this->session->remove('form.failed');
        }
    }

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        Form::$types['captcha'] = 'ReCaptcha_Element';
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

        }
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
    public function check_action($value) {
        if ($this->recaptcha->check_answer($this->input->ip_address(), $this->input->post('recaptcha_challenge_field'), $value)) {
            return TRUE;
        } else {
            $this->form_validation->set_message('check_captcha', $this->lang->line('recaptcha_incorrect_response'));
            return FALSE;
        }
    }

}