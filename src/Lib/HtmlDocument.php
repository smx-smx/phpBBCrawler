<?php

namespace Lib;

/**
 * @author Stefano Moioli <smxdev4@gmail.com>
 */
class HtmlDocument {
	private $dom;

	public function __construct($html){
		libxml_use_internal_errors(true);
		$this->dom = new \DOMDocument();
		$this->dom->encoding = 'utf-8';
		$this->dom->loadHTML(mb_convert_encoding($html,	'HTML-ENTITIES', 'UTF-8'));
	}
	
	private function getCharset(){
		$charset = \YaLinqo\Enumerable::from($this->xpath("//meta"))
				->where(function($node){
					return $node->hasAttribute('charset');
				})
				->select(function($node){
					return $node->getAttribute('charset');
				})
				->firstOrDefault();
				
		return $charset;
	}
	
	public static function fromNode(\DOMNode $node, $charset = 'utf-8'){
		$html = $node->ownerDocument->saveHTML($node);
		$html = "<meta charset=\"{$charset}\">" . $html;
		
		$doc = new HtmlDocument($html);
		assert($doc->getCharset() == $charset);
		return $doc;
	}
	
	/**
	 * 
	 * @param type $query
	 * @return \DOMElement[]
	 */
	public function xpath($query){
		$xp = new \DOMXPath($this->dom);
		$nodes = $xp->query($query);
		$n = $nodes->count();
		
		$results = array();
		for($i=0; $i<$n; $i++){
			$results[] = $nodes->item($i);
		}
		return $results;
	}
}