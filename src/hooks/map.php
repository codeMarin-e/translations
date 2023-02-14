<?php
return [
    implode(DIRECTORY_SEPARATOR, [ base_path(), 'resources', 'views', 'components', 'admin', 'box_sidebar.blade.php']) => [
        "{{--  @HOOK_ADMIN_SIDEBAR  --}}" => "\t<x-admin.sidebar.translations_option />\n",
    ],
    implode(DIRECTORY_SEPARATOR, [ base_path(), 'config', 'marinar.php']) => [
        "// @HOOK_MARINAR_CONFIG_ADDONS" => "\t\t\\Marinar\\Translations\\MarinarTranslations::class, \n"
    ]
];
