<?php

/**
 * Output
 *
 * @author		Dmitriy Belyaev <admin@cogear.ru>
 * @copyright		Copyright (c) 2011, Dmitriy Belyaev
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 * @package		Core
 * @subpackage
 * @version		$Id$
 */
class Response extends Core_ArrayObject {
    /**
     * HTTP codes
     *
     * @var  array
     */
    public static $codes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found', // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        507 => 'Insufficient Storage',
        509 => 'Bandwidth Limit Exceeded'
    );
    /**
     * Default response code
     * @var int
     */
    private $status = 200;
    /**
     * Headers
     * @var Core_ArrayObject
     */
    protected $headers = array();
    /**
     * Flag indicates headers are send
     * 
     * @var boolean 
     */
    private $headers_sent = FALSE;

    /**
     * Constructor
     */
    public function __construct() {
        $cogear = getInstance();
        $this->headers = new Core_ArrayObject($this->headers);
        hook('exit',array($this,'send'));
    }

    /**
     * Get headers
     *
     * @return array
     */
    public function getHeaders() {
        return $this->headers->toArray();
    }

    /**
     * Set headers
     *
     * @param array $headers
     */
    public function setHeaders(array $headers) {
        $this->headers->exchangeArray($headers);
    }

    /**
     * Set header
     * 
     * @param string $name
     * @param string $data
     */
    public function header($name, $data) {
        $this->headers->offsetSet($name, $data);
    }

    /**
     * Send headers
     */
    public function sendHeaders() {
        if (!$this->headers_sent) {
            $cogear = getInstance();
            if (!$protocol = $cogear->request->get('SERVER_PROTOCOL')) {
                $protocol = 'HTTP/1.1';
            }
            @header($protocol . ' ' . $this->status . ' ' . $this->codes[$this->status]);
            foreach ($this->headers as $name => $value) {
                @header($name . ': ' . $value, TRUE);
            }
            $this->headers_sent = TRUE;
        }
    }
    /**
     * Send response
     */
    public function send() {
        $this->sendHeaders();
        foreach($this as $value){
            echo $value;
        }
    }
}

function redirect($url = NULL){
    $url OR $url = Url::link();
    cogear()->save();
    header('Location: '.$url);
    exit;
}
function history($step = -1){
    $cogear = getInstance();
    redirect($cogear->session->history($step,'/'));
}
function back(){
    history(-1);
}