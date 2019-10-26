<?php

namespace PhpBB;

use Lib\Curl;
use Lib\HtmlDocument;

/**
 * @author Stefano Moioli <smxdev4@gmail.com>
 */
class Website {
	private $baseUrl;
	/**
	 *
	 * @var Curl
	 */
	private $curl;
	
	public function __construct($name, $baseUrl){
		$this->baseUrl = $baseUrl;
		if(substr($baseUrl, -1) !== '/'){
			$this->baseUrl .= '/';
		}
		
		$this->curl = new Curl("{$name}.txt");
	}
	
	private static function relativizeUrl($url){
		if(strpos($url, "./") === 0)
				return substr($url, 2);
		return $url;
	}
	
	public function get($url){
		//$url = self::relativizeUrl($url);
		return $this->curl->get($this->baseUrl . $url);
	}
	
	public function post($url, $body = null){
		//$url = self::relativizeUrl($url);
		return $this->curl->post($this->baseUrl . $url, null, $body);
	}
	
	public function login($username, $password){	
		$html = $this->get("ucp.php?mode=login");		
		$doc = new HtmlDocument($html);
		
		$sidNodes = $doc->xpath("//input[@type='hidden'][@name='sid']/@value");
		if(count($sidNodes) == 0)
			return false;	
		$sid = $sidNodes[0]->textContent;
		
		$redirect = array_map(function($itm){
			return $itm->textContent;
		}, $doc->xpath("//input[@type='hidden'][@name='redirect']/@value"));
	
		$result = $this->post("ucp.php?mode=login", array(
			'username' => $username,
			'password' => $password,
			'autologin' => 'on',
			'redirect' => $redirect,
			'sid' => $sid,
			'login' => 'Login'
		));
		return true;
	}
	
	public function getForumGroups(){
		$html = $this->get("index.php");
				
		$doc = new HtmlDocument($html);
		
		$forums = $doc->xpath("//ul[@class='topiclist']//*[@class='list-inner']/a");
		return array_map(function($element){
			return new ForumGroup($this, $element);
		}, $forums);		
	}
}