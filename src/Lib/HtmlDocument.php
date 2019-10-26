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
		$this->dom->loadHTML($html);
	}
	
	public static function fromNode(\DOMNode $node){
		$html = $node->ownerDocument->saveHTML($node);
		return new HtmlDocument($html);
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