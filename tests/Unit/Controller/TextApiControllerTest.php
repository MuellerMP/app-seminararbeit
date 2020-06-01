<?php

namespace OCA\SeminarTest\Tests\Unit\Controller;

use OCA\SeminarTest\Controller\TextApiController;

class NoteApiControllerTest extends TextControllerTest {
	public function setUp(): void {
		parent::setUp();
		$this->controller = new TextApiController(
			'seminararbeit', $this->request, $this->service, $this->userId
		);
	}
}
