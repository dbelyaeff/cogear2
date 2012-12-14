<?php

/**
 * Шестерёнка ReCaptcha
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class ReCaptcha_Gear extends Gear {

    protected $hooks = array(
        'form.init.user-login' => 'hookLoginForm',
        'form.result.after' => 'hookFormResult',
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
     * Конструктор
     */
    public function __construct($config) {
        parent::__construct($config);
        Form::$types['captcha'] = 'ReCaptcha_Element';
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