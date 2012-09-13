<?php

class Curl extends Core_ArrayObject{

        public $ch;
        public $cookie_file;
        public $ua = "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3";
        public $encoding = 'utf-8';
        public $headers = array();
        public $referer = '';
        public $followlocation = FALSE;
        public $url = NULL;
        public $site = NULL;
        public $port = 80;
        /**
        * Constructor
        */
        public function curl($options = array()){
                $this->init($options);
        }
        
        /**
        * Headers set
        *
        * @param        mixed   Headers
        * @return       void
        */
        public function headers($headers = NULL){
                if(is_string($headers)) $headers = explode("\n",trim($headers));
                $this->headers = empty($headers) ? array(
                        'Content-type: text/html; charset="'.$this->encoding.'"',
                        'Accept: */*',
                        'Keep-Alive: 300',
                        'Connection: keep-alive',
                        'If-Modified-Since: '.date("D, d m Y H:i:s",time()-60*60*24*7).' GMT',
                        'Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7',
                        'Accept-Language: ru,en-us;q=0.7,en;q=0.3',
                        'Cache-Control: no-cache',
                        'User-Agent: '.$this->ua,
                        'Expect:',
                        ) : $headers;
                curl_setopt($this->ch, CURLOPT_HEADER, $this->headers);
                curl_setopt($this->ch, CURLOPT_HEADER, FALSE);
        }
        
        /**
        * Initialize request
        */
        public function init($options = array()){
                foreach($options as $key=>$value){
                        $this->$key = $value;
                }
                $this->ch = curl_init($this->url);
                $this->headers();
                if(empty($this->cookie_file)) $this->cookie_file = dirname(__FILE__).'/cookies.txt';
                curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, $this->followlocation);
                curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookie_file);
                curl_setopt($this->ch, CURLOPT_COOKIEJAR,  $this->cookie_file);
                curl_setopt($this->ch, CURLOPT_RETURNTRANSFER,TRUE);
                curl_setopt($this->ch, CURLOPT_USERAGENT, $this->ua);
                curl_setopt($this->ch, CURLOPT_AUTOREFERER, TRUE);
                curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Expect:')); 
                curl_setopt($this->ch,CURLOPT_SSL_VERIFYHOST,FALSE);
                curl_setopt($this->ch,CURLOPT_SSL_VERIFYPEER,FALSE);
                curl_setopt($this->ch,CURLOPT_PORT,$this->port);
        }
        
        /**
        * Set referer
        * @param        string
        */
        public function referer($referer){
                curl_setopt($this->ch, CURLOPT_REFERER, $referer);
        }
        
        /**
        * Get url
        * @param        string  Url
        * @param        array   Data
        * @return       string
        */
        public function get($url,$data = array()){
                if(!empty($data)){
                        $url .= '?'.http_build_query($data);
                } 
                return $this->request($url,$data);
        }
        
        /**
        * POST request
        *
        * @param        string  Url
        * @param        array   Data
        * @return       string
        */
        public function post($url,$data = array()){
                return $this->request($url,$data,'POST');
        }
        
        /**
        * Request
        *
        * @param        string  url
        * @param        array   data
        * @param        string  type
        * @return       string
        */
        private function request($url,$data = array(), $type = 'GET'){
                if(!empty($this->site) && strpos($url,'http://') === FALSE) $url = $this->site.$url;
                $this->referer(empty($this->referer) ? $url : $this->referer);
                curl_setopt($this->ch, CURLOPT_POST, $type == 'POST' ? TRUE : FALSE);
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $type == 'POST' ? http_build_query($data) : FALSE);
                curl_setopt($this->ch, CURLOPT_URL, $url);
                $this->referer = $url;
                $return = curl_exec($this->ch);
                debug($return);
                return empty($return) ?  curl_error($this->ch) : $return;
        }
        
        /**
        * Clear
        */
        public function clear(){
                @unlink($this->cookie_file);
                curl_close($this->ch);
        }
        
        /**
        * Destructor
        */
        public function __destruct(){
                $this->clear();
        }       
        
        /**
        * Show info
        */
        public function info($opt = 0){
                return curl_getinfo($this->ch,$opt);
        }
}