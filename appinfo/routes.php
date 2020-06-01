<?php

return [
	'resources' => [
		'text' => ['url' => '/texts'],
		'text_api' => ['url' => '/api/0.1/texts']
	],
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
		['name' => 'text_api#preflighted_cors', 'url' => '/api/0.1/{path}',
			'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']]
	]
];
