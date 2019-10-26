<?php

namespace PhpBB;

/**
 * @author Stefano Moioli <smxdev4@gmail.com>
 */
class ForumGroup {
	private $site;
	
	private $name;
	private $url;
	
	public function getSite(){
		return $this->site;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getId(){
		$qs = parse_url($this->url, PHP_URL_QUERY);
		parse_str($qs, $params);
		return $params['f'];
	}
	
	public function getUrl(){
		return $this->url;
	}
	
	public function getForums(){
		$html = $this->site->get($this->url);
		$doc = new \Lib\HtmlDocument($html);
		
		$forums = $doc->xpath("//ul[contains(@class, 'forums')]//div[@class='list-inner']");
		return array_map(function($element){
			return new Forum($this, $element);
		}, $forums);
	}
	
	public function __construct(Website $site, \DOMElement $element){
		$this->site = $site;
		$this->url = $element->getAttribute('href');
		$this->name = $element->textContent;
	}
}
