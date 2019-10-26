<?php

namespace PhpBB;

abstract class AttachmentType {
	const Picture = 0;
	const File = 1;
}

/**
 * @author Stefano Moioli <smxdev4@gmail.com>
 */
class Attachment {
	private $site;
	private $post;
	
	private $url;
	private $fileName;
	
	public function getPost(){
		return $this->post;
	}
	
	public function download(){
		return $this->site->get($this->url);
	}
	
	public function getUrl(){
		return $this->url;
	}
	
	public function getId(){
		$qs = parse_url($this->url, PHP_URL_QUERY);
		parse_str($qs, $params);
		return $params['id'];
	}
	
	public function getFileName(){
		return $this->fileName;
	}
	
	public function __construct(Post $post, \DOMElement $attachment, $attachmentType){
		$this->post = $post;
		$this->site = $post->getSite();
		
		switch($attachmentType){
			case AttachmentType::Picture:
				$this->fileName = $attachment->getAttribute('alt');
				$this->url = $attachment->getAttribute('src');
				break;
			case AttachmentType::File:
				$this->fileName = $attachment->textContent;
				$this->url = $attachment->getAttribute('href');
				break;
		}
	}
}