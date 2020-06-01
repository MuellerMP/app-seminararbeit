<?php

namespace OCA\SeminarTest\Controller;

use PHPUnit\Framework\TestCase;

use OCP\AppFramework\Http\TemplateResponse;

class PageControllerTest extends TestCase {
	private $controller;

	public function setUp(): void {
		$request = $this->getMockBuilder('OCP\IRequest')->getMock();
		$this->controller = new PageController('seminararbeit', $request);
	}


	public function testIndex() {
		$result = $this->controller->index();

		$this->assertEquals('main', $result->getTemplateName());
		$this->assertTrue($result instanceof TemplateResponse);
	}
}
