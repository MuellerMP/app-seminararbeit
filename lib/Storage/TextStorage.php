<?php
namespace OCA\SeminarTest\Storage;

class TextStorage {

    private $storage;
	/** @var string */
	private $userId;

    public function __construct($storage,$userId){
        $this->storage = $storage->getUserFolder($userId);
		if(!$this->storage->nodeExists('/texts/')) {
			$this->storage->newFolder('/texts/');
		}
    }

    public function writeText($filename, $content) {
        // check if file exists and write to it if possible
        try {
			$path = '/texts/' . $filename . '.md';
            try {
                $file = $this->storage->get($path);
            } catch(\OCP\Files\NotFoundException $e) {
                $this->storage->newFile($path);
                $file = $this->storage->get($path);
            }

            $file->putContent($content);

        } catch(\OCP\Files\NotPermittedException $e) {
            // you have to create this exception by yourself ;)
            throw new StorageException('Cant write to file');
        }
    }
	
	public function assertDoesntAlreadyExist($filename) {
		$path = '/texts/' . $filename . '.md';
		if($this->storage->nodeExists($path)) {
            throw new StorageException('File already exists');
		}
	}
	
	public function getContent($filename) {
		$path = '/texts/' . $filename . '.md';
        try {
            return $file = $this->storage->get($path)->getContent();
        } catch(\OCP\Files\NotFoundException $e) {
            throw new StorageException('File does not exist');
        }
    }
	
	public function deleteText($filename) {
		$path = '/texts/' . $filename . '.md';
		if($this->storage->nodeExists($path)) {
			$this->storage->get($path)->delete();
		}
	}
}