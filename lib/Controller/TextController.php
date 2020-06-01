<?php

namespace OCA\SeminarTest\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use \OCP\BackgroundJob\IJobList;

use OCA\SeminarTest\Service\TextService;
use OCA\SeminarTest\Service\TextWithContent;
use OCA\SeminarTest\Cron\CleanupJob;

class TextController extends Controller {
	/** @var TextService */
	private $service;

	/** @var string */
	private $userId;
	
	/** @var IJobList */
	private $jobList;

	use Errors;

	public function __construct($appName,
								IRequest $request,
								TextService $service,
								IJobList $jobList,
								$userId) {
		parent::__construct($appName, $request);
		$this->service = $service;
		$this->jobList = $jobList;
		$this->userId = $userId;
	}

	/**
	 * @NoAdminRequired
	 */
	public function index(): DataResponse {
		return new DataResponse($this->service->findAll($this->userId));
	}

	/**
	 * @NoAdminRequired
	 */
	public function show(int $id): DataResponse {
		return $this->handleNotFound(function () use ($id) {
			return $this->service->find($id, $this->userId);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(string $title, string $filename, string $content): DataResponse {
		return new DataResponse($this->fetchTextWithContent($title, $filename, $content));
	}
	
	private function fetchTextWithContent(string $title, string $filename,
										  string $content) : TextWithContent {
		$textWithContent = $this->service->create($title, $filename,
												  $content, $this->userId);
		$arguments = array("id" => $textWithContent->getId(), "userId" => $this->userId);
		$this->jobList->add(CleanupJob::class, $arguments);
		return $textWithContent;
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $id, string $title, string $filename, string $content): DataResponse {
		return $this->handleNotFound(function () use ($id, $title, $filename, $content) {
			return $this->service->update($id, $title, $filename, $content, $this->userId);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $id): DataResponse {
		return $this->handleNotFound(function () use ($id) {
			return $this->service->delete($id, $this->userId);
		});
	}
}
