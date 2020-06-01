<?php

namespace OCA\SeminarTest\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\SeminarTest\Db\Text;
use OCA\SeminarTest\Db\TextMapper;

use OCA\SeminarTest\Storage\TextStorage;

class TextService {

	/** @var TextMapper */
	private $mapper;

	/** @var textStorage */
	private $textStorage;

	public function __construct(TextMapper $mapper, TextStorage $textStorage) {
		$this->mapper = $mapper;
		$this->textStorage = $textStorage;
	}

	public function findAll(string $userId): array {
		$textsWithContent = array();
		$texts = $this->mapper->findAll($userId);
		foreach($texts as $text) {
			try {
				$textWithContent = $this->loadContent($text);
			} catch(Exception $e) {
				$this->mapper->delete($text);
				continue;
			}
			array_push($textsWithContent, $textWithContent);
		}
		return $textsWithContent;
	}
	
	private function loadContent($text): TextWithContent {
		$textWithContent = new TextWithContent();
		$textWithContent->setId($text->getId());
		$textWithContent->setTitle($text->getTitle());
		$textWithContent->setFilename($text->getFilename());
		$textWithContent->setUserId($text->getUserId());
		$textWithContent->setContent($this->textStorage->getContent($text->getFilename()));
		return $textWithContent;
	}

	private function handleException(Exception $e): void {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
			throw new TextNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

	public function find($id, $userId) {
		try {
			$text = $this->mapper->find($id, $userId);
			return $this->loadContent($text);

			// in order to be able to plug in different storage backends like files
		// for instance it is a good idea to turn storage related exceptions
		// into service related exceptions so controllers and service users
		// have to deal with only one type of exception
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create($title, $filename, $content, $userId) {
		$this->textStorage->assertDoesntAlreadyExist($filename);
		$this->textStorage->writeText($filename, $content);
		$text = new Text();
		$text->setTitle($title);
		$text->setFilename($filename);
		$text->setUserId($userId);
		$this->mapper->insert($text);
		$textWithContent = $this->loadContent($text);
		return $textWithContent;
	}

	public function update($id, $title, $filename, $content, $userId) {
		try {
			$text = $this->mapper->find($id, $userId);
			if($text->getFilename() != $filename) {
				$this->textStorage->deleteText($text->getFilename());
			}
			$this->textStorage->writeText($filename, $content);
			$text->setTitle($title);
			$text->setFilename($filename);
			$this->mapper->update($text);
			$textWithContent = $this->loadContent($text);
			return $textWithContent;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function delete($id, $userId) {
		try {
			$text = $this->mapper->find($id, $userId);
			$textWithContent = $this->loadContent($text);
			$this->textStorage->deleteText($text->getFilename());
			$this->mapper->delete($text);
			return $textWithContent;
		} catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
