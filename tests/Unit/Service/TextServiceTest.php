<?php

namespace OCA\SeminarTest\Tests\Unit\Service;

use OCA\SeminarTest\Service\TextNotFound;
use PHPUnit\Framework\TestCase;

use OCP\AppFramework\Db\DoesNotExistException;

use OCA\SeminarTest\Db\Text;
use OCA\SeminarTest\Service\TextService;
use OCA\SeminarTest\Db\TextMapper;

class TextServiceTest extends TestCase {
	private $service;
	private $mapper;
	private $userId = 'john';

	public function setUp(): void {
		$this->mapper = $this->getMockBuilder(NoteMapper::class)
			->disableOriginalConstructor()
			->getMock();
		$this->service = new TextService($this->mapper);
	}

	public function testUpdate() {
		// the existing note
		$text = Text::fromRow([
			'id' => 3,
			'title' => 'yo',
			'fileName' => 'test',
			'content' => 'nope'
		]);
		$this->mapper->expects($this->once())
			->method('find')
			->with($this->equalTo(3))
			->will($this->returnValue($text));

		// the text when updated
		$updatedText = Text::fromRow(['id' => 3]);
		$updatedNote->setTitle('title');
		$updatedNote->setFileName('fileName');
		$updatedNote->setContent('content');
		$this->mapper->expects($this->once())
			->method('update')
			->with($this->equalTo($updatedNote))
			->will($this->returnValue($updatedNote));

		$result = $this->service->update(3, 'title', 'fileName', 'content', $this->userId);

		$this->assertEquals($updatedNote, $result);
	}

	public function testUpdateNotFound() {
		$this->expectException(NoteNotFound::class);
		// test the correct status code if no note is found
		$this->mapper->expects($this->once())
			->method('find')
			->with($this->equalTo(3))
			->will($this->throwException(new DoesNotExistException('')));

		$this->service->update(3, 'title', 'fileName', 'content', $this->userId);
	}
}
