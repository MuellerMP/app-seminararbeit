<?php

namespace OCA\SeminarTest\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Text extends Entity implements JsonSerializable {
	protected $title;
	protected $filename;
	protected $userId;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'title' => $this->title,
			'filename' => $this->filename
		];
	}
}
