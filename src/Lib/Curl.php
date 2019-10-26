<?php

namespace Lib;

/**
 * @author Stefano Moioli <smxdev4@gmail.com>
 */
class Curl {
	private $curl;
	private $cookieFile;
	
	public function __construct($cookieFile=null){
		$this->cookieFile = $cookieFile;
	}
	
	private function init($url){
		$this->curl = curl_init($url);
		$this->setOptions(array(
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:70.0) Gecko/20100101 Firefox/70.0'
		));
		
		if(!is_null($this->cookieFile)){
			$this->setOptions(array(
				CURLOPT_COOKIEFILE => $this->cookieFile,
				CURLOPT_COOKIEJAR => $this->cookieFile
			));
		}
	}
	
	public function setOptions(array $opts){
		curl_setopt_array($this->curl, $opts);
	}
	
	public function setOption($opt, $val){
		curl_setopt($this->curl, $opt, $val);
	}
	
	public function get($url, $params = null){
		$this->init($url);
		if(!is_null($params)){
			$url .= "?" . http_build_query($params);
		}
		
		$this->setOption(CURLOPT_POST, false);
		return curl_exec($this->curl);
	}
	
	public function post($url, $params = null, $body = null){
		$this->init($url);
		if(!is_null($params)){
			$url .= "?" . http_build_query($params);
		}		
		$this->setOption(CURLOPT_POST, true);
		if(!is_null($body)){
			$this->setOption(CURLOPT_POSTFIELDS, http_build_query($body));
		}
		
		return curl_exec($this->curl);
	}
}