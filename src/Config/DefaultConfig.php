<?php

return array(
    // For maintenance
    'read_only' => array(
        'default' => false,
    ),
    // Site metadata
    'site_icons' => array(
        'default' => array(
            'favicon' => '/favicon.ico',
            'site_apple_touch_icon' => false,
        ),
    ),
    'site_canonical_name' => array(
        'default' => 'iSAIS校務資訊系統',
    ),
    'site_logos' => array(
        'default' => array(
            'logo' => false,
            'wordmark' => false,
        ),
    ),
    // Internationalization
    'site_lang' => array(
        'default' => 'zh-hant',
    ),
    'local_time_zone' => array(
        'default' => 'UTC',
    ),
    // Server
    'server' => array(
        'default' => false,
    ),
    'force_https' => array(
        'default' => false,
        'type' => 'boolean',
    ),
    'base_dir' => array(
        'default' => '/tkufd',
    ),
    'script_path' => array(
        'default' => false,
    ),
    'index_script' => array(
        'default' => false,
    ),
    'load_script' => array(
        'default' => false,
    ),
    'rest_script' => array(
        'default' => false,
    ),
    'page_path' => array(
        'default' => false,
    ),
    'main_page_use_domain_root' => array(
        'default' => true,
        'type' => 'boolean',
    ),
    // Database
    'db_type' => array(
        'default' => 'mysql',
    ),
    'db_server' => array(
        'default' => 'localhost',
    ),
    'db_port' => array(
        'default' => 5432,
    ),
    'db_use_ssl' => array(
        'default' => false,
    ),
    'db_name' => array(
        'default' => 'isais',
    ),
    'db_options' => array(
        'default' => 'DEFAULT CHARACTER SET=utf8 DEFAULT COLLATE=utf8_unicode_ci',
    ),
    'db_table_prefix' => array(
        'default' => '',
    ),
    'db_table_options' => array(
        'default' => 'ENGINE=InnoDB, DEFAULT CHARSET=utf8mb4',
    ),
    'db_username' => array(
        'default' => 'isaisuser',
    ),
    'db_password' => array(
        'default' => '',
    ),
    'db_admin_username' => array(
        'default' => false,
    ),
    'db_admin_password' => array(
        'default' => false,
    ),
    'shared_db_name' => array(
        'default' => false,
    ),
    'shared_db_table_prefix' => array(
        'default' => '',
    ),
    'shared_db_tables' => array(
        'default' => array(),
    ),
    // Cache
    'main_cache_type' => array(
        'default' => false,
    ),
    // User
    'user_group_rights' => array(
        'default' => array(),
    ),
);
