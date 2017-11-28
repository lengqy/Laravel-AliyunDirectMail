<?php 

return [
	'directmail' => [
		'access_key_id'     => env('DIRECT_MAIL_KEY'),
		'access_secret'     => env('DIRECT_MAIL_SECRET'),
		'replay_to_address' => env('DIRECT_MAIL_REPLAY_TO', 'false'),
		'address_type'      => env('DIRECT_MAIL_ADDRESS_TYPE', '1'),
		'region'            => env('DIRECT_MAIL_REGION', 'cn-hangzhou'),
		'click_trace'       => env('DIRECT_MAIL_CLICK_TRACE', '0'),
	]
];