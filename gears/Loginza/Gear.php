<?php

/**
 *  Loginza gear
 * 
 *  Public serivce to login and register on site via variety of social services
 * 
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage          Loginza
 * @version		$Id$
 */
class Loginza_Gear extends Gear {

    protected $name = 'Loginza';
    protected $description = 'Public serivce to login and register on site via variety of social services.';
    protected $type = Gear::MODULE;
    protected $package = 'Social';
    protected $order = 0;
    protected $api;

    /**
     * Init
     */
    public function init() {
        parent::init();
        $cogear = getInstance();
        $this->api = new Loginza_API();
        hook('form.user-login.result.before', array($this, 'hookUserForm'));
        hook('form.user-register.result.before', array($this, 'hookUserForm'));
        hook('form.user-profile.init', array($this, 'hookUserProfile'));
    }

    /**
     * Delete link
     * 
     * @param int $id 
     */
    public function delete_action($id) {
        $loginza = new Db_ORM('users_loginza');
        $loginza->id = $id;
        if ($loginza->find() && ($this->user->id == $id OR access('loginza delete_all'))) {
            $loginza->delete();
            Ajax::json(array(
                'items' => array(
                    array(
                        'id' => 'loginza-' . $id,
                        'action' => 'delete',
                    )
                ),
                'message' => array(
                    'body' => t('Link between your profile and social service was successfully deleted.'),
                ),
            ));
        } else {
            Ajax::denied();
        }
    }

    /**
     * Set avatar from social account
     * 
     * @param type $id 
     */
    public function avatar_action($id) {
        $loginza = new Db_ORM('users_loginza');
        $loginza->id = $id;
        if ($loginza->find() && ($this->user->id == $id OR access('loginza delete_all'))) {
            if ($loginza->photo) {
                $user = new User_Object();
                $user->id = $this->user->id;
                $path = UPLOADS.DS.'avatars'.DS.$this->user->id.DS.basename($loginza->photo);
                copy($loginza->photo,$path);
                $user->avatar = Url::toUri($path,UPLOADS);
                $user->save();
                Ajax::json(array(
                    'action' => 'reload',
                ));
            }
        } else {
            Ajax::denied();
        }
    }

    /**
     * Hooking user login and register forms
     * 
     * @param object $Form 
     */
    public function hookUserForm($Form) {
        js('http://loginza.ru/js/widget.js');
        $Form->addElement('loginza', array(
            'type' => 'span',
            'value' => HTML::a($this->api->getWidgetUrl(Url::link() . 'loginza'), HTML::img($this->folder . '/img/sign_in_button_gray.gif', t('Log in via social services', 'Loginza')), array('class' => 'loginza')),
        ));
    }

    /**
     * Hooking user profile form
     * 
     * @param object $Form 
     */
    public function hookUserProfile($Form) {
        $data['social'] = array(
            'type' => 'tab',
            'label' => t('Social')
        );
        $data['social_info'] = array(
            'type' => 'div',
            'value' => t('<p>There you can attach social accounts to integrate with your profile. It will help you for quick login.</p>'),
        );
        if ($connected_accounts = $this->db->where('uid', $Form->object->id)->get('users_loginza')->result()) {
            $tpl = new Template('Loginza.accounts');
            $tpl->accounts = $connected_accounts;
            $data['loginza_accounts'] = array(
                'type' => 'div',
                'value' => $tpl->render(),
            );
        }
        js('http://loginza.ru/js/widget.js');
        $data['loginza'] = array(
            'type' => 'div',
            'value' => HTML::a($this->api->getWidgetUrl(Url::link() . 'loginza'), HTML::img($this->folder . '/img/sign_in_button_gray.gif', t('Log in via social services', 'Loginza')), array('class' => 'loginza')),
        );
        $Form->elements->place($data, 'submit', Form::BEFORE);
    }

    /**
     * Default dispatcher
     * 
     * @param string $action 
     */
    public function index_action($action = '', $subaction = NULL) {
        if (!empty($_POST['token'])) {
            // Get the profile of authorized user
            $UserProfile = $this->api->getAuthInfo($_POST['token']);
            // Check for errors
            if (!empty($UserProfile->error_type)) {
                // Debug info for developer
                error(t($UserProfile->error_type . ": " . $UserProfile->error_message));
            } elseif (empty($UserProfile)) {
                error(t('Temporary error with Loginza authentification.'));
            } else {
                $this->session->loginza = $UserProfile;
            }
        }
        if ($loginza = $this->session->loginza) {
            /**
             * There we have 3 ways of workflow
             * 
             * 1. User is logged in. Add new identity to database if it's not exist.
             * 2. User is registred. Authorize.
             * 3. User is not registred. Show register form connected and fullfilled with Loginza data (login, e-mail and so on).
             */
            $user = new Db_ORM('users_loginza');
            $user->identity = $loginza->identity;
            // If user is logged in
            if ($this->user->id) {
                // If integration is found
                if ($user->find()) {
                    // If integration belongs to the current user
                    if ($user->uid == $this->user->id) {
                        $user->loginza->data = json_encode($loginza);
                        $user->update();
                        flash_info(t('Your integration with profile <b>%s</b> was updated successfully.', 'Loginza', $loginza->identity), t('Updated succeed.'));
                    }
                    // If integration is used with another account
                    else {
                        flash_error(t('Profile <b>%s</b> is integrated with sombody else account. You cannot use it before someone would left it out.', 'Loginza', $loginza->identity), t('Update failure.'));
                    }
                }
                // If integration is not found
                else {
                    // Create new database record
                    $user->uid = $this->user->id;
                    $user->provider = $loginza->provider;
                    $UserProfile = new Loginza_UserProfile($loginza);
                    isset($loginza->photo) && $user->photo = $loginza->photo;
                    $user->full_name = $UserProfile->genFullName();
                    $user->data = json_encode($loginza);
                    $user->save();
                }
                $this->session->loginza = NULL;
                // Redirect to user profile
                redirect(Url::gear('user') . 'edit/#tab-social');
            }
            // If user is a guest he has to login or even to register
            else {
                // Record found в†’ try to log in
                if ($user->find()) {
                    $search = new User_Object();
                    $search->id = $user->uid;
                    if ($search->find()) {
                        $this->user->forceLogin($user->uid, 'id');
                    } else {
                        flash_error(t('Cannot find user with id <b>%s</b>.', 'Loginza', $user->uid));
                    }
                    $this->session->loginza = NULL;
                    // This tiny little redirect caused error by Loginza "Invalid / empty session data! Retry auth.:
                    // Left it where it is for memories.
                    // Important! Do not uncomment!
                    //back();
                }
                // If record wasn't found в†’ register user with special data
                else {
                    if (!access('user register')) {
                        return info('You don\'t have an access to registration');
                    }
                    success('First step of registration is done. Please, fill some fields to complete your registration.');

                    $form = new Form('User.register');
                    $UserProfile = new Loginza_UserProfile($loginza);
                    $tpl = new Template('Loginza.register');
                    $tpl->loginza = $loginza;
                    $tpl->profile = $UserProfile;
                    append('content', $tpl->render());
                    $data['login'] = $UserProfile->genFullName();
                    isset($loginza->email) && $data['email'] = $loginza->email;
                    $form->setValues($data);
                    if ($data = $form->result()) {
                        $this->user->attach($data);
                        $this->user->hashPassword();
                        if ($uid = $this->user->save()) {
                            // Create new database record
                            $user->uid = $uid;
                            $user->provider = $loginza->provider;
                            $UserProfile = new Loginza_UserProfile($loginza);
                            isset($loginza->photo) && $user->photo = $loginza->photo;
                            $user->full_name = $UserProfile->genFullName();
                            $user->data = json_encode($loginza);
                            $user->save();
                        }
                        $this->session->loginza = NULL;
                        flash_success('User was successfully registered! Please, check your email for further instructions.', 'Registration succeed.');
                        redirect();
                    }
                    append('content', $form->render());
                }
            }
        }
    }

}