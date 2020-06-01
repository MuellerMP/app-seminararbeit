<?php

namespace OCA\SeminarTest\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\ApiController;

use OCA\SeminarTest\Service\TextService;

class TextApiController extends ApiController {
	/** @var TextService */
	private $service;

	/** @var string */
	private $userId;

	use Errors;

	public function __construct($appName,
								IRequest $request,
								TextService $service,
								$userId) {
		parent::__construct($appName, $request);
		$this->service = $service;
		$this->userId = $userId;
	}

	/**
	 * @CORS
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function index(): DataResponse {
		return new DataResponse($this->service->findAll($this->userId));
	}

	/**
	 * @CORS
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function show(int $id): DataResponse {
		return $this->handleNotFound(function () use ($id) {
			return $this->service->find($id, $this->userId);
		});
	}

	/**
	 * @CORS
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function create(string $title, string $filename, string $content): DataResponse {
		return new DataResponse($this->service->create($title, $filename, $content,
			$this->userId));
	}

	/**
	 * @CORS
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function update(int $id, string $title, string $filename,
						   string $content): DataResponse {
		return $this->handleNotFound(function () use ($id, $title, $filename, $content) {
			return $this->service->update($id, $title, $filename, $content, $this->userId);
		});
	}

	/**
	 * @CORS
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function destroy(int $id): DataResponse {
		return $this->handleNotFound(function () use ($id) {
			return $this->service->delete($id, $this->userId);
		});
	}
}
