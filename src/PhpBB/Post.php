<?php

namespace PhpBB;

use DOMElement;
use Lib\HtmlDocument;

/**
 * @author Stefano Moioli <smxdev4@gmail.com>
 */
class Post {
	private $site;
	private $topic;
	
	private $authorNick;
	private $postDate;
	
	private $content;
	
	private $url;
	
	private $attachments = array();
	
	function getTopic(){
		return $this->topic;
	}
	
	function getAuthorNick(){
		return $this->authorNick;
	}
	
	function getPostDate() {
		return $this->postDate;
	}

	function getContent() {
		return $this->content;
	}

	function getAttachments() {
		return $this->attachments;
	}

	public function getUrl(){
		return $this->topic->getUrl() . $this->url;
	}	
	
	public function getId(){
		return substr($this->url, 1);
	}
	
	public function getSite(){
		return $this->site;
	}
	
	public function __construct(Topic $topic, DOMElement $postBody){
		$this->site = $topic->getSite();
		$this->topic = $topic;
		
		$doc = HtmlDocument::fromNode($postBody);
		
		$heading = $doc->xpath("//div[contains(@id, 'post_content')]/h3/a")[0];
		$this->url = $heading->getAttribute('href');
		
		/** @var DOMElement $authorUser */
		$responsiveHide = $doc->xpath("//p[@class='author']//span[@class='responsive-hide']")[0];
		
		$aDoc = HtmlDocument::fromNode($responsiveHide);
		$author = $aDoc->xpath("//a[contains(@class, 'username')]");
		if(count($author) != 0){
			$authorUser = $author[0];
		} else {
			$authorUser = $aDoc->xpath("//span[@class='username']")[0];
		}
		
		$this->authorNick = $authorUser->textContent;
		$dateStr = $responsiveHide->nextSibling->textContent;
		$this->postDate = strtolower($dateStr);
		
		/** @var DOMElement $content */
		$content = $doc->xpath("//div[@class='content']")[0];
		$innerHtml = $content->ownerDocument->saveHTML($content);
		$this->content = $innerHtml;
		
		///// ATTACHMENTS
		$files = $doc->xpath("//dl[@class='file']//a[@class='postlink']");
		foreach($files as $element){
			$this->attachments[] = new Attachment($this, $element, AttachmentType::File);
		}
		
		$pictures = $doc->xpath("//dt[@class='attach-image']/img[@class='postimage']");
		foreach($pictures as $element){
			$this->attachments[] = new Attachment($this, $element, AttachmentType::Picture);
		}
	}
}
