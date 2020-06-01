<?php

namespace OCA\SeminarTest\Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;

use OCP\AppFramework\Http;
use OCP\IRequest;

use OCA\SeminarTest\Service\TextNotFound;
use OCA\SeminarTest\Service\TextService;
use OCA\SeminarTest\Controller\TextController;

class TextControllerTest extends TestCase {
	protected $controller;
	protected $service;
	protected $userId = 'john';
	protected $request;

	public function setUp(): void {
		$this->request = $this->getMockBuilder(IRequest::class)->getMock();
		$this->service = $this->getMockBuilder(NoteService::class)
			->disableOriginalConstructor()
			->getMock();
		$this->controller = new TextController(
			'seminararbeit', $this->request, $this->service, $this->userId
		);
	}

	public function testUpdate() {
		$text = 'just check if this value is returned correctly';
		$this->service->expects($this->once())
			->method('update')
			->with($this->equalTo(3),
					$this->equalTo('title'),
					$this->equalTo('content'),
					$this->equalTo('fileName'),
				   $this->equalTo($this->userId))
			->will($this->returnValue($text));

		$result = $this->controller->update(3, 'title', 'fileName', 'content');

		$this->assertEquals($text, $result->getData());
	}


	public function testUpdateNotFound() {
		// test the correct status code if no text is found
		$this->service->expects($this->once())
			->method('update')
			->will($this->throwException(new NoteNotFound()));

		$result = $this->controller->update(3, 'title', 'fileName', 'content');

		$this->assertEquals(Http::STATUS_NOT_FOUND, $result->getStatus());
	}
}
