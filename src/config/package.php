<?php
	return [
		'install' => [
            'php artisan db:seed --class="\Marinar\Translations\Database\Seeders\MarinarTranslationsInstallSeeder"',
		],
        'remove' => [
            'php artisan db:seed --class="\Marinar\Translations\Database\Seeders\MarinarTranslationsRemoveSeeder"',
        ]
	];
