<?php

/**
 * @author Stefano Moioli <smxdev4@gmail.com>
 */
class Exporter {
	private $site;
	private $name;
	
	public function __construct($name, $baseUrl){
		$this->name = $name;
		$this->site = new PhpBB\Website($name, $baseUrl);
	}
	
	public function login($username, $password){
		$this->site->login($username, $password);
	}
	
	private function exportSite(PhpBB\Website $site){
		$groups = "{$this->name}/groups";		
		@mkdir($this->name);
		@mkdir($groups);
		
		foreach($site->getForumGroups() as $group){
			print("[GROUP] {$group->getUrl()}" . PHP_EOL);
			$this->exportGroup($group);
		}
	}
	
	private function exportGroup(PhpBB\ForumGroup $group){
		$dir = "{$this->name}/groups/{$group->getId()}";
		@mkdir($dir);
		@mkdir("{$dir}/forums");
		
		file_put_contents("{$dir}/data", serialize($group));
		
		foreach($group->getForums() as $forum){
			print("[FORUM] {$forum->getUrl()}" . PHP_EOL);
			$this->exportForum($forum);
		}
	}
	
	private function exportForum(\PhpBB\Forum $forum){				
		$group = $forum->getGroup();
		$dir = "{$this->name}/groups/{$group->getId()}/forums/{$forum->getId()}" ;
		@mkdir($dir);
		@mkdir("{$dir}/topics");
		
		file_put_contents("{$dir}/data", serialize($forum));
		
		foreach($forum->getTopics() as $topic){
			print("[TOPIC] {$topic->getUrl()}" . PHP_EOL);
			$this->exportTopic($topic);
		}
	}
	
	private function exportTopic(PhpBB\Topic $topic){		
		$forum = $topic->getForum();
		$group = $forum->getGroup();
		$dir = "{$this->name}/groups/{$group->getId()}/forums/{$forum->getId()}/topics/{$topic->getId()}";
		@mkdir($dir);
		@mkdir("{$dir}/posts");
		
		file_put_contents("{$dir}/data", serialize($topic));
		
		foreach($topic->getPosts() as $post){
			print("[POST] {$post->getUrl()}" . PHP_EOL);
			$this->exportPost($post);
		}
	}
	
	private function exportPost(\PhpBB\Post $post){
		$topic = $post->getTopic();
		$forum = $topic->getForum();
		$group = $forum->getGroup();
		$dir = "{$this->name}/groups/{$group->getId()}/forums/{$forum->getId()}/topics/{$topic->getId()}/posts/{$post->getId()}";
		@mkdir($dir);
		@mkdir("{$dir}/attachments");
				
		file_put_contents("{$dir}/data", serialize($post));
		
		foreach($post->getAttachments() as $attach){
			print("[ATTACH] {$attach->getUrl()}" . PHP_EOL);
			$this->exportAttachment($attach);
		}
	}
	
	private function exportAttachment(PhpBB\Attachment $attach){
		$post = $attach->getPost();
		$topic = $post->getTopic();
		$forum = $topic->getForum();
		$group = $forum->getGroup();
		$dir = "{$this->name}/groups/{$group->getId()}/forums/{$forum->getId()}/topics/{$topic->getId()}/posts/{$post->getId()}/attachments/{$attach->getId()}";
		@mkdir($dir);
		
		$data = $attach->download();
		file_put_contents("{$dir}/" . $attach->getFileName(), $data);
	}
	
	public function run(){
		$this->exportSite($this->site);
	}
}
