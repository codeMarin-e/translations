<?php
namespace Marinar\Translations\Database\Seeders;

use Illuminate\Database\Seeder;
use Marinar\Translations\MarinarTranslations;

class MarinarTranslationsRemoveSeeder extends Seeder {

    use \Marinar\Marinar\Traits\MarinarSeedersTrait;

    public static function configure() {
        static::$packageName = 'marinar_translations';
        static::$packageDir = MarinarTranslations::getPackageMainDir();
    }

    public function run() {
        if(!in_array(env('APP_ENV'), ['dev', 'local'])) return;

        $this->autoRemove();

        $this->refComponents->info("Done!");
    }

}
