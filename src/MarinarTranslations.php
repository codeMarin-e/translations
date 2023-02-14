<?php
namespace Marinar\Translations;

use Marinar\Translations\Database\Seeders\MarinarTranslationsInstallSeeder;

class MarinarTranslations {

    public static function getPackageMainDir() {
        return __DIR__;
    }

    public static function injects() {
        return MarinarTranslationsInstallSeeder::class;
    }
}
