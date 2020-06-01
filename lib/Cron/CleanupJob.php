<?php
namespace OCA\SeminarTest\Cron;

use \OCP\BackgroundJob\TimedJob;
use OCA\SeminarTest\Service\TextService;

class CleanupJob extends TimedJob {

	/** @var TextService */
    private $service;

    public function __construct(ITimeFactory $time, TextService $service) {
        parent::__construct($time);
        $this->service = $service;

        // Run every 30s
        parent::setInterval(30);
    }

    public function run($arguments) {
        $this->service->delete($arguments['id'], $arguments['userId']);
    }

}