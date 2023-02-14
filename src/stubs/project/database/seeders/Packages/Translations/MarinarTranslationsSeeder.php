<?php
namespace Database\Seeders\Packages\Translations;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MarinarTranslationsSeeder extends Seeder {

    public function run() {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::upsert([
            ['guard_name' => 'admin', 'name' => 'translations.view'],
            ['guard_name' => 'admin', 'name' => 'translation.update'],
        ], ['guard_name','name']);
    }
}
