<?php
    namespace Marinar\Translations\Database\Seeders;

    use Illuminate\Database\Seeder;
    use Marinar\Translations\MarinarTranslations;

    class MarinarTranslationsInstallSeeder extends Seeder {

        use \Marinar\Marinar\Traits\MarinarSeedersTrait;

        public static function configure() {
            static::$packageName = 'marinar_translations';
            static::$packageDir = MarinarTranslations::getPackageMainDir();
        }

        public function run() {
            if(!in_array(env('APP_ENV'), ['dev', 'local'])) return;

            $this->autoInstall();

            $this->refComponents->info("Done!");
        }

        private function installMe() {
            $this->givePermissions(implode(DIRECTORY_SEPARATOR, [
                static::$packageDir, '..', '..', 'waavi_translation', '.git', '.objects'
            ]), true);
        }

    }
