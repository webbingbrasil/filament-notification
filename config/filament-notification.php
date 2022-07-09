<?php

return [
	// in case you have larger messages in average, you can adjust the width of the popup here:
	'width'   => '300px',

	'feed' => [
		// read notifications are displayed grayed-out by default.
		// set this to false to dont display them at all.
		'displayReadNotifications' => true,
	],

	'buttons' => [
		'markAllRead' => [
			'color' => 'primary',
			'outlined' => false,
			'icon' => 'filament-notification::icon-check-all',
			'size' => 'sm',
		],
	],
];
