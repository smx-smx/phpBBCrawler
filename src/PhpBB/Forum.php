<?php

namespace PhpBB;

use DOMElement;
use Lib\HtmlDocument;
use YaLinqo\Enumerable;

/**
 * @author Stefano Moioli <smxdev4@gmail.com>
 */
class Forum {
	use Traits\ForumMultiPage;
	
	private $site;
	private $group;
		
	private $url;
	private $name;
	private $description;

	function getName() {
		return $this->name;
	}
	
	public function getSite(){
		return $this->site;
	}
	
	public function getGroup(){
		return $this->group;
	}
	
	public function getId(){
		$qs = parse_url($this->url, PHP_URL_QUERY);
		parse_str($qs, $params);
		return $params['f'];
	}
	
	public function getUrl(){
		return $this->url;
	}

	public function __construct(ForumGroup $group, DOMElement $listInner){
		$this->group = $group;	
		$this->site = $group->getSite();
		
		$this->description = $listInner->childNodes[1]->textContent;
		
		$a = HtmlDocument::fromNode($listInner)->xpath("//a[@class='forumtitle']")[0];
		$this->name = $a->textContent;
		$this->url = $a->getAttribute('href');
	}
	
	public function getTopics(){
		$html = $this->site->get($this->url);
		$doc = new HtmlDocument($html);
		
		$lastPage = $this->getLastPage($doc);
		
		$paginationText = trim($doc->xpath("//div[@class='pagination']")[0]->textContent);
		$numberOfTopics = intval(explode(' ', $paginationText)[0]);

		$topics = array();
		
		$topicXpath = "//ul[@class='topiclist topics']//dl[contains(@class, 'row-item')]//div[@class='list-inner']";
		
		$nodes = $doc->xpath("//div[@class='forumbg announcement']" . $topicXpath);
		foreach($nodes as $element){
			$tdoc = HtmlDocument::fromNode($element);
			$aUser = $tdoc->xpath("//a[contains(@class, 'username')]")[0];
			
			$textNode = utf8_decode($aUser->parentNode->textContent);
			if(strpos($textNode, "Posted in") !== FALSE){
				// links to another forum, skip!
				continue;
			}
			$topics[] = new Topic($this, $element);
		}

		$offset = 0;
		for($i=1; $i<=$lastPage; $i++){
			$nodes = $doc->xpath("//div[@class='forumbg']" . $topicXpath);
			foreach($nodes as $element){
				$topics[] = new Topic($this, $element);
			}
			
			$arrowNext = $this->getArrowNext($doc);
			if(count($arrowNext) == 0)
				break;
			
			$offset += count($nodes);			
			$url = $this->url . "&start={$offset}";
			
			$html = $this->site->get($url);
			$doc = new HtmlDocument($html);
		}
		
		//assert(count($topics) == $numberOfTopics);
		return $topics;
	}
}
