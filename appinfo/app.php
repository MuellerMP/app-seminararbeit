<?php
namespace OCA\SeminarTest\AppInfo;

use \OCP\AppFramework\App;

use \OCA\SeminarTest\Storage\TextStorage;


class Application extends App {

    public function __construct(array $urlParams=array()){
        parent::__construct('seminararbeit', $urlParams);

        $container = $this->getContainer();

        /**
         * Storage Layer
         */
        $container->registerService('textStorage', function($c) {
            return new TextStorage($c->query('RootStorage'),
			$c->query('userId'));
        });

        $container->registerService('RootStorage', function($c) {
            return $c->query('ServerContainer')->getRootFolder();
        });

    }
}