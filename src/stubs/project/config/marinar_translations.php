<?php
return [
    /**\
     * If there is no set uri - to check for uri in fallback language
     */
    'use_fallback' => true,

    /**
     * Behavior when package is removed from composer
     * true - delete all
     * false - delete all, but not changed stubs files
     * 1 - delete all, but keep the stub files and injection
     * 2 - keep everything
     */
    'delete_behavior' => false,

    /**
     * File stubs that return arrays that are configurable,
     * If path is directory - its files and sub directories
     */
    'values_stubs' => [
        __DIR__,
    ],

    /**
     * Exclude stubs to be updated
     * If path is directory - exclude all its files
     * If path is file - only it
     */
    'exclude_stubs' => [
    ],

    /**
     * Exclude addon injections
     * file_path => [ '@hook1', '@hook2' ]
     * file_path => * - all from this file
     */
    'exclude_injects' => [],

    /**
     * Addons hooked to the package
     */
    'addons' => [
        // @HOOK_CONFIGS_ADDONS
    ],
    // @HOOK_CONFIGS
];
