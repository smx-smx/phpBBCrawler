<?php
namespace PhpBB;

/**
 * @author Stefano Moioli <smxdev4@gmail.com>
 */
class Topic {
	use Traits\ForumMultiPage;
	
	private $site;
	private $forum;
	
	private $url;
	private $name;
	
	private $opNick;
	private $opDate;
	
	public function getName(){
		return $this->name;
	}
	
	public function getSite(){
		return $this->site;
	}
	
	public function getForum(){
		return $this->forum;
	}
	
	public function getId(){
		$qs = parse_url($this->url, PHP_URL_QUERY);
		parse_str($qs, $params);
		return $params['t'];
	}
	
	public function getUrl(){
		return $this->url;
	}
	
	public function __construct(Forum $forum, \DOMElement $listInner){
		$this->forum = $forum;
		$this->site = $forum->getSite();
		
		$doc = \Lib\HtmlDocument::fromNode($listInner);

		$aTopic = $doc->xpath("//a[@class='topictitle']")[0];
		$this->url = $aTopic->getAttribute('href');
		$this->name = $aTopic->textContent;
		
		/** @var \DOMElement $aUser */
		$user = $doc->xpath("//a[contains(@class, 'username')]");
		if(count($user) > 0){
			$user = $user[0];
		} else {
			$user = $doc->xpath("//span[@class='username']")[0];
		}
		
		$this->opNick = $user->textContent;
		//print($this->name . "[{$this->getId()}]\n");
		$dateStr = utf8_decode($user->parentNode->textContent);
		$dateStr = explode('« ', $dateStr)[0];
		$this->opDate = strtotime($dateStr);
	}
	
	/**
	 * 
	 * @return \PhpBB\Post[]
	 */
	public function getPosts(){
		$html = $this->site->get($this->url);
		$doc = new \Lib\HtmlDocument($html);
		$lastPage = $this->getLastPage($doc);
		
		$posts = array();				
		$offset = 0;
		for($i=1; $i<=$lastPage; $i++){
			$nodes = $doc->xpath("//div[@class='postbody']");
			foreach($nodes as $element){
				$posts[] = new Post($this, $element);
			}
			
			$arrowNext = $this->getArrowNext($doc);
			if(count($arrowNext) == 0)
				break;
			
			$offset += count($nodes);			
			$url = $this->url . "&start={$offset}";
			
			$html = $this->site->get($url);
			$doc = new \Lib\HtmlDocument($html);
		}
		
		return $posts;
	}
}