<?php

namespace Isais\Config;

use Isais\Config\MainConfigNames;

class MainConfigSchema
{
    private static $config_schema = array(
        // For maintenance
        MainConfigNames::CONFIG_READ_ONLY => array(
            'default' => false,
        ),
        // Site metadata
        MainConfigNames::CONFIG_SITE_FAVICON => array(
            'default' => '113dbb/113dbb04/favicon.ico',
        ),
        MainConfigNames::CONFIG_SITE_APPLE_TOUCH_ICON => array(
            'default' => '113dbb/113dbb04/apple_touch_icon.png',
        ),
        MainConfigNames::CONFIG_SITE_CANONICAL_NAME => array(
            'default' => 'iSAIS校務資訊系統',
        ),
        MainConfigNames::CONFIG_SITE_LOGOS => array(
            'default' => array(
                'logo' => false,
                'wordmark' => false,
            ),
        ),
        // Internationalization
        MainConfigNames::CONFIG_SITE_LANGUAGE => array(
            'default' => 'zh-hant',
        ),
        MainConfigNames::CONFIG_LOCAL_TIME_ZONE => array(
            'default' => 'UTC',
        ),
        // Server
        MainConfigNames::CONFIG_PROTOCOL => array(
            'default' => 'http://',
        ),
        MainConfigNames::CONFIG_SERVER => array(
            'default' => false,
        ),
        MainConfigNames::CONFIG_FORCE_HTTPS => array(
            'default' => false,
            'type' => 'boolean',
        ),
        MainConfigNames::CONFIG_BASE_DIR => array(
            'default' => '113dbb/113dbb04/',
        ),
        MainConfigNames::CONFIG_SCRIPT_PATH => array(
            'default' => '113dbb/113dbb04/tkuisais/',
        ),
        MainConfigNames::CONFIG_INDEX_SCRIPT => array(
            'default' => '113dbb/113dbb04/tkuisais/index.php',
        ),
        MainConfigNames::CONFIG_LOAD_SCRIPT => array(
            'default' => '113dbb/113dbb04/tkuisais/load.php',
        ),
        MainConfigNames::CONFIG_API_SCRIPT => array(
            'default' => '113dbb/113dbb04/tkuisais/api.php',
        ),
        MainConfigNames::CONFIG_PAGE_PATH => array(
            'default' => '113dbb/113dbb04/$1',
        ),
        MainConfigNames::CONFIG_MAIN_PAGE_USE_ROOT => array(
            'default' => 'directory_root',
        ),
        // Database
        MainConfigNames::CONFIG_DB_TYPE => array(
            'default' => 'mysql',
        ),
        MainConfigNames::CONFIG_DB_SERVER => array(
            'default' => 'localhost',
        ),
        MainConfigNames::CONFIG_DB_PORT => array(
            'default' => 3306,
        ),
        MainConfigNames::CONFIG_DB_USE_SSL => array(
            'default' => false,
        ),
        MainConfigNames::CONFIG_DB_NAME => array(
            'default' => '113dbb04',
        ),
        MainConfigNames::CONFIG_DB_OPTIONS => array(
            'default' => 'DEFAULT CHARACTER SET=utf8 DEFAULT COLLATE=utf8_unicode_ci',
        ),
        MainConfigNames::CONFIG_DB_TABLE_PREFIX => array(
            'default' => '',
        ),
        MainConfigNames::CONFIG_DB_TABLE_OPTIONS => array(
            'default' => 'ENGINE=InnoDB, DEFAULT CHARSET=utf8mb4',
        ),
        MainConfigNames::CONFIG_DB_USERNAME => array(
            'default' => 'root',
        ),
        MainConfigNames::CONFIG_DB_PASSWORD => array(
            'default' => '',
        ),
        MainConfigNames::CONFIG_DB_ADMIN_USERNAME => array(
            'default' => false,
        ),
        MainConfigNames::CONFIG_DB_ADMIN_PASSWORD => array(
            'default' => false,
        ),
        MainConfigNames::CONFIG_SHARED_DB_NAME => array(
            'default' => false,
        ),
        MainConfigNames::CONFIG_SHARED_DB_TABLE_PREFIX => array(
            'default' => '',
        ),
        MainConfigNames::CONFIG_SHARED_DB_TABLES => array(
            'default' => array(),
        ),
        // Cache
        MainConfigNames::CONFIG_MAIN_CACHE_TYPE => array(
            'default' => false,
        ),
        // User
        MainConfigNames::CONFIG_USER_GROUP_RIGHTS => array(
            'default' => array(),
        ),
    );

    public static function getConfigSchema()
    {
        return self::$config_schema;
    }
}
