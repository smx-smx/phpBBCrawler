<?php

namespace PhpBB\Traits;

use Lib\HtmlDocument;
use YaLinqo\Enumerable;

/**
 * @author Stefano Moioli <smxdev4@gmail.com>
 */
trait ForumMultiPage {
	private function getPagination(HtmlDocument $doc){
		return $doc->xpath("//div[@class='pagination']/ul/li");
	}
	
	private function getArrowNext(HtmlDocument $doc){
		return $doc->xpath("//div[@class='pagination']/ul/li[@class='arrow next']");
	}
	
	private function getLastPage(HtmlDocument $doc){
		$pagination = $this->getPagination($doc);
		
		return Enumerable::from($pagination)
				->where(function($itm){
					return $itm->getAttribute('class') == 'arrow next';
				})
				->select(function($itm, $key) use($pagination){
					return $pagination[$key - 1]->childNodes[0]->textContent;
				})
				->firstOrDefault(1);
	}
}
