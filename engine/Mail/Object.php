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
class Mail_Object {

    protected $from = '';
    protected $from_name = '';
    protected $to = array();
    protected $subject = '';
    protected $body = '';
    protected $encoding = 'utf-8';
    protected $signature = '';
    protected $attachments = array();

    /**
     * Constructor
     * 
     * @param string $from
     * @param string $from_name
     * @param array $to
     * @param string $subject
     * @param string $body 
     */
    public function __construct($from = NULL, $from_name = NULL, $to = NULL, $subject = NULL, $body = NULL) {
        $from && $this->from($from);
        $from_name && $this->fromName($from_name);
        $to && $this->to($to);
        $subject && $this->subject($subject);
        $body && $this->body($body);
        $signature = config('mail.signature');
    }

    /**
     * Set email "from" param.
     * 
     * @param string $from 
     */
    public function from($from) {
        $this->from = $from;
        return $this;
    }

    /**
     * Set email "from_name" param.
     * 
     * @param string $from 
     */
    public function fromName($from_name) {
        $this->from_name = $from_name;
        return $this;
    }

    /**
     * Set email "to" param.
     * 
     * @param array $from 
     */
    public function to(array $to) {
        $this->to = array_unique(array_merge($this->to, $to));
        return $this;
    }

    /**
     * Set email "subject" param.
     * 
     * @param type $subject 
     */
    public function subject($subject) {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Set email "body" param.
     * 
     * @param type $body 
     */
    public function body($body) {
        $this->body = $body;
        return $this;
    }

    /**
     * Set email "encoding" param.
     * 
     * @param type $encoding 
     */
    public function encoding($encoding) {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Set email "signature" param.
     * 
     * @param type $signature 
     */
    public function signature($signature) {
        $this->signature = $signature;
        return $this;
    }

    /**
     * Set email "attachments" param.
     * 
     * @param array|string $files 
     */
    public function attach($files) {
        $this->attachments = array_unique(array_merge($this->attachments, (array) $files));
        return $this;
    }

    /**
     * Send email
     */
    public function send() {
        $mail = new Mail_PHPMailer();

        $mail->IsSendmail();


        $mail->AddReplyTo($this->from, $this->from_name);
        $mail->SetFrom($this->from, $this->from_name);
        foreach ($this->to as $address) {
            $mail->AddAddress($address);
        }

        $mail->Subject = $this->subject;

        $mail->AltBody = strip_tags($this->body);

        $mail->MsgHTML($this->body);
        foreach ($this->attachments as $attachment) {
            $mail->AddAttachment($attachment);
        }
        if (!$mail->Send()) {
            error($mail->ErrorInfo);
            return FALSE;
        } else {
            return TRUE;
        }
    }

}