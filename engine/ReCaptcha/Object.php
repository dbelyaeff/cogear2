<?php

/**
 * Recaptcha object
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class ReCaptcha_Object extends Options {

    /**
     * Constructor
     */
    public function __construct($options = array()) {
        parent::__construct($options);
        if ($defaults = Config('ReCaptcha')) {
            $this->options = $defaults->adopt($this->options);
        }
    }

    /**
     * Encodes the given data into a query string format
     * @param $data - array of string elements to be encoded
     * @return string - encoded request
     */
    public function qsencode($data) {
        $req = "";
        foreach ($data as $key => $value)
            $req .= $key . '=' . urlencode(stripslashes($value)) . '&';

        // Cut the last '&'
        $req = substr($req, 0, strlen($req) - 1);
        return $req;
    }

    /**
     * Submits an HTTP POST to a reCAPTCHA server
     *
     * @param string $host
     * @param string $path
     * @param array $data
     * @param int port
     * @return array response
     */
    public function http_post($host, $path, $data, $port = 80) {

        $req = $this->qsencode($data);

        $http_request = "POST $path HTTP/1.0\r\n";
        $http_request .= "Host: $host\r\n";
        $http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
        $http_request .= "Content-Length: " . strlen($req) . "\r\n";
        $http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
        $http_request .= "\r\n";
        $http_request .= $req;
        // @todo Rewrite with Curl Object
        $response = '';
        if (false == ( $fs = @fsockopen($host, $port, $errno, $errstr, 10) )) {
            error(t('Could not open socket','ReCaptcha'));
        } else {
            fwrite($fs, $http_request);

            while (!feof($fs))
                $response .= fgets($fs, 1160); // One TCP-IP packet
            fclose($fs);
            $response = explode("\r\n\r\n", $response, 2);
        }
        return $response;
    }

    /**
     * Gets the challenge HTML (javascript and non-javascript version).
     * This is called from the browser, and the resulting reCAPTCHA HTML widget
     * is embedded within the HTML form it was called from.
     * @param string $error The error given by reCAPTCHA (optional, default is null)
     * @param boolean $use_ssl Should the request be made over ssl? (optional, default is false)

     * @return string - The HTML to be embedded in the user's form.
     */
    public function get_html_code($error = null, $use_ssl = false) {
        $pubkey = $this->options->public;
        if (!$pubkey OR strlen($pubkey) != 40) {
            error(t("To use reCAPTCHA you must get an API key from <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a>","ReCaptcha"));
            return;
        }

        if ($use_ssl) {
            $server = $this->options->api_secure_server;
        } else {
            $server = $this->options->api_server;
        }

        $errorpart = "";
        if ($error) {
            $errorpart = "&amp;error=" . $error;
        }
        return ' <script type="text/javascript">
 var RecaptchaOptions = {
    theme : \''.$this->options->theme.'\',
    lang: \''.config('i18n.lang').'\'
 };
 </script><script type="text/javascript" src="' . $server . '/challenge?k=' . $pubkey . $errorpart . '"></script>

	<noscript>
  		<iframe src="' . $server . '/noscript?k=' . $pubkey . $errorpart . '" height="300" width="500" frameborder="0"></iframe><br/>
  		<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
  		<input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
	</noscript>';
    }

    /**
     * Calls an HTTP POST function to verify if the user's guess was correct
     * 
     * @param array $extra_params an array of extra variables to post to the server
     * @return ReCaptchaResponse
     */
    public function check_answer( $extra_params = array()) {
        $privkey = $this->options->private;
        $remoteip = cogear()->session->ip;
        $challenge = cogear()->input->post('recaptcha_challenge_field');
        $response = cogear()->input->post('recaptcha_response_field');
        if ($privkey == null || $privkey == '') {
            error(t("To use reCAPTCHA you must get an API key from <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a>",'ReCaptcha'));
        }

        if ($remoteip == null || $remoteip == '') {
            error(t("For security reasons, you must pass the remote ip to reCAPTCHA",'ReCaptcha'));
        }



        //discard spam submissions
        if ($challenge == null || strlen($challenge) == 0 || $response == null || strlen($response) == 0) {
            $recaptcha_response = new ReCaptcha_Response();
            $recaptcha_response->is_valid = false;
            $recaptcha_response->error = 'incorrect-captcha-sol';
            return $recaptcha_response;
        }

        $response = $this->http_post($this->options->verify_server, "/recaptcha/api/verify", array(
            'privatekey' => $privkey,
            'remoteip' => $remoteip,
            'challenge' => $challenge,
            'response' => $response
                ) + $extra_params
        );

        $answers = explode("\n", $response [1]);
        $recaptcha_response = new ReCaptcha_Response();

        if (trim($answers [0]) == 'true') {
            $recaptcha_response->is_valid = true;
        } else {
            $recaptcha_response->is_valid = false;
            $recaptcha_response->error = $answers [1];
        }
        return $recaptcha_response;
    }

    /**
     * gets a URL where the user can sign up for reCAPTCHA. If your application
     * has a configuration page where you enter a key, you should provide a link
     * using this function.
     * @param string $domain The domain where the page is hosted
     * @param string $appname The name of your application
     */
    public function get_signup_url($domain = null, $appname = null) {
        return "https://www.google.com/recaptcha/admin/create?" . $this->qsencode(array('domains' => $domain, 'app' => $appname));
    }
    /**
     * Do smthg
     *
     * @param type $val
     * @return type
     */
    function aes_pad($val) {
        $block_size = 16;
        $numpad = $block_size - (strlen($val) % $block_size);
        return str_pad($val, strlen($val) + $numpad, chr($numpad));
    }

    /* Mailhide related code */

    public function aes_encrypt($val, $ky) {
        if (!function_exists("mcrypt_encrypt")) {
            error("To use reCAPTCHA Mailhide, you need to have the mcrypt php module installed.");
            return;
        }
        $mode = MCRYPT_MODE_CBC;
        $enc = MCRYPT_RIJNDAEL_128;
        $val = $this->aes_pad($val);
        return mcrypt_encrypt($enc, $ky, $val, $mode, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
    }

    /**
     * Do smthg
     *
     * @param type $x
     * @return type
     */
    public function mailhide_urlbase64($x) {
        return strtr(base64_encode($x), '+/', '-_');
    }

    /* gets the reCAPTCHA Mailhide url for a given email, public key and private key */

    public function mailhide_url($pubkey, $privkey, $email) {
        if ($pubkey == '' || $pubkey == null || $privkey == "" || $privkey == null) {
            error("To use reCAPTCHA Mailhide, you have to sign up for a public and private key, " .
                    "you can do so at <a href='http://www.google.com/recaptcha/mailhide/apikey'>http://www.google.com/recaptcha/mailhide/apikey</a>");
        }


        $ky = pack('H*', $privkey);
        $cryptmail = $this->aes_encrypt($email, $ky);

        return "http://www.google.com/recaptcha/mailhide/d?k=" . $pubkey . "&c=" . $this->mailhide_urlbase64($cryptmail);
    }

    /**
     * gets the parts of the email to expose to the user.
     * eg, given johndoe@example,com return ["john", "example.com"].
     * the email is then displayed as john...@example.com
     */
    public function mailhide_email_parts($email) {
        $arr = preg_split("/@/", $email);

        if (strlen($arr[0]) <= 4) {
            $arr[0] = substr($arr[0], 0, 1);
        } else if (strlen($arr[0]) <= 6) {
            $arr[0] = substr($arr[0], 0, 3);
        } else {
            $arr[0] = substr($arr[0], 0, 4);
        }
        return $arr;
    }

    /**
     * Gets html to display an email address given a public an private key.
     * to get a key, go to:
     *
     * http://www.google.com/recaptcha/mailhide/apikey
     */
    public function mailhide_html($pubkey, $privkey, $email) {
        $emailparts = $this->mailhide_email_parts($email);
        $url = $this->mailhide_url($pubkey, $privkey, $email);

        return htmlentities($emailparts[0]) . "<a href='" . htmlentities($url) .
                "' onclick=\"window.open('" . htmlentities($url) . "', '', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300'); return false;\" title=\"Reveal this e-mail address\">...</a>@" . htmlentities($emailparts [1]);
    }

}