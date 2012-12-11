<?php

/**
 *  Form Element ReCaptcha
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 *         Form

 */
class ReCaptcha_Element extends Form_Element_Abstract {

    /**
     * Process elements value from request
     *
     * @return
     */
    public function result($value=NULL) {
        $recaptcha = new ReCaptcha_Object();
        $resp = $recaptcha->check_answer();
        if (!$resp->is_valid) {
            // What happens when the CAPTCHA was entered incorrectly
            $this->addError(t("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
                    "(reCAPTCHA said: %s)",'ReCaptcha',$resp->error));
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Render
     */
    public function render() {
        $this->prepareOptions();
        $recaptcha = new ReCaptcha_Object();
        $this->code = $recaptcha->get_html_code($this->name);
        $this->decorate();
        return $this->code;
    }

}