<?php

/**
 * Mail object
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Mail_Object extends Object{

    public $options = array(
        'name' => '',
        'from' => '',
        'from_name' => '',
        'to' => array(),
        'subject' => '',
        'body' => '',
        'charset' => 'utf-8',
        'signature' => '',
        'attachments' => array(),
        'smtp' => array(
            'login' => '',
            'password' => '',
            'host' => '',
            'port' => 25,
        ),
    );

    /**
     * Constructor
     *
     * @param string $from
     * @param string $from_name
     * @param array $to
     * @param string $subject
     * @param string $body
     */
    public function __construct($options) {
        parent::__construct($options);
        $this->options->extend(config('mail'));
        event('mail.'.$this->name);
    }

    /**
     * Set email "from" param.
     *
     * @param string $from
     */
    public function from($from) {
        $this->options->from = $from;
        return $this;
    }

    /**
     * Set email "from_name" param.
     *
     * @param string $from
     */
    public function fromName($from_name) {
        $this->options->from_name = $from_name;
        return $this;
    }

    /**
     * Set email "to" param.
     *
     * @param array $from
     */
    public function to($to) {
       is_string($to) && $to = explode(',', $to);
       $this->options->to->extend($to);
        return $this;
    }

    /**
     * Set email "subject" param.
     *
     * @param type $subject
     */
    public function subject($subject) {
        $this->options->subject = $subject;
        return $this;
    }

    /**
     * Set email "body" param.
     *
     * @param type $body
     */
    public function body($body) {
        $this->options->body = $body;
        return $this;
    }

    /**
     * Set email "signature" param.
     *
     * @param type $signature
     */
    public function signature($signature) {
        $this->options->signature = $signature;
        return $this;
    }

    /**
     * Set email "attachments" param.
     *
     * @param array|string $files
     */
    public function attach($files) {
        $this->options->attachments = array_unique(array_merge($this->options->attachments, (array) $files));
        return $this;
    }

    /**
     * Send email
     */
    public function send() {
        $mail = new Mail_PHPMailer();
        $this->charset && $mail->CharSet = $this->charset;
        if($this->smtp->login && $this->smtp->host){
            $mail->IsSMTP();
            $mail->Username = $this->smtp->login;
            $mail->Password = $this->smtp->password;
            $mail->Host = $this->smtp->host;
            $mail->SMTPAuth = TRUE;
            $mail->SMTPDebug = TRUE;
        }
        else {
            $mail->IsSendmail();
        }
        $this->from OR $this->from = config('mail.from');
        $this->from_name OR $this->from_name = config('mail.from_name');
        $mail->AddReplyTo($this->from, $this->from_name);
        $mail->SetFrom($this->from, $this->from_name);
        foreach ($this->to as $address) {
            $mail->AddAddress($address);
        }

        $mail->Subject = $this->subject;
        if($this->signature){
            $this->options->body .= $this->signature;
        }
        $mail->AltBody = strip_tags($this->body);
        $mail->MsgHTML($this->body);
        foreach ($this->attachments as $attachment) {
            $mail->AddAttachment($attachment);
        }
        event('Mail.send',$mail);
        if (!$mail->Send()) {
            error($mail->ErrorInfo);
            return FALSE;
        } else {
            return TRUE;
        }
    }

}