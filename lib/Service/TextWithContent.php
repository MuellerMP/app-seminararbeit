<?php

namespace OCA\SeminarTest\Service;

use JsonSerializable;

class TextWithContent implements JsonSerializable {
	protected $id;
	protected $title;
	protected $filename;
	protected $content;
	protected $userId;
	
	public function getContent($content): string {
		return $this->content;
	}
	
	public function setContent($content) {
		$this->content = $content;
	}
	
	public function setId($id) {
		$this->id = $id;
	}
	public function getId() {
		return $this->id;
	}
	public function setTitle($title) {
		$this->title = $title;
	}
	public function setFilename($filename) {
		$this->filename = $filename;
	}
	public function setUserId($userId) {
		$this->userId = $userId;
	}

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'title' => $this->title,
			'filename' => $this->filename,
			'content' => $this->content
		];
	}
}
