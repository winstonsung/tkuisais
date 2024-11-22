<?php

namespace Isais\Config;

class MainConfigNames {
    // For maintenance

    const CONFIG_READ_ONLY = 'read_only';

    // Site metadata

    const CONFIG_SITE_FAVICON = 'site_favicon';

    const CONFIG_SITE_APPLE_TOUCH_ICON = 'site_apple_touch_icon';

    const CONFIG_SITE_CANONICAL_NAME = 'site_canonical_name';

    const CONFIG_SITE_LOGOS = 'site_logos';

    // Internationalization

    const CONFIG_SITE_LANGUAGE = 'site_language';

    const CONFIG_LOCAL_TIME_ZONE = 'local_time_zone';

    // Server

    const CONFIG_PROTOCOL = 'protocol';

    const CONFIG_SERVER = 'server';

    const CONFIG_FORCE_HTTPS = 'force_https';

    const CONFIG_BASE_DIR = 'base_dir';

    const CONFIG_SCRIPT_PATH = 'script_path';

    const CONFIG_INDEX_SCRIPT = 'index_script';

    const CONFIG_LOAD_SCRIPT = 'load_script';

    const CONFIG_API_SCRIPT = 'api_script';

    const CONFIG_PAGE_PATH = 'page_path';

    const CONFIG_MAIN_PAGE_USE_ROOT = 'main_page_use_root';

    // Database

    const CONFIG_DB_TYPE = 'db_type';

    const CONFIG_DB_SERVER = 'db_server';

    const CONFIG_DB_PORT = 'db_port';

    const CONFIG_DB_USE_SSL = 'db_use_ssl';

    const CONFIG_DB_NAME = 'db_name';

    const CONFIG_DB_OPTIONS = 'db_options';

    const CONFIG_DB_TABLE_PREFIX = 'db_table_prefix';

    const CONFIG_DB_TABLE_OPTIONS = 'db_table_options';

    const CONFIG_DB_USERNAME = 'db_username';

    const CONFIG_DB_PASSWORD = 'db_password';

    const CONFIG_DB_ADMIN_USERNAME = 'db_admin_username';

    const CONFIG_DB_ADMIN_PASSWORD = 'db_admin_password';

    const CONFIG_SHARED_DB_NAME = 'shared_db_name';

    const CONFIG_SHARED_DB_TABLE_PREFIX = 'shared_db_table_prefix';

    const CONFIG_SHARED_DB_TABLES = 'shared_db_tables';

    // Cache

    const CONFIG_MAIN_CACHE_TYPE = 'main_cache_type';

    // User

    const CONFIG_USER_GROUP_RIGHTS = 'user_group_rights';

    private static $valid_config_options = array(
        self::CONFIG_READ_ONLY,
        self::CONFIG_SITE_FAVICON,
        self::CONFIG_SITE_APPLE_TOUCH_ICON,
        self::CONFIG_SITE_CANONICAL_NAME,
        self::CONFIG_SITE_LOGOS,
        self::CONFIG_SITE_LANGUAGE,
        self::CONFIG_LOCAL_TIME_ZONE,
        self::CONFIG_PROTOCOL,
        self::CONFIG_SERVER,
        self::CONFIG_FORCE_HTTPS,
        self::CONFIG_BASE_DIR,
        self::CONFIG_SCRIPT_PATH,
        self::CONFIG_INDEX_SCRIPT,
        self::CONFIG_LOAD_SCRIPT,
        self::CONFIG_API_SCRIPT,
        self::CONFIG_PAGE_PATH,
        self::CONFIG_MAIN_PAGE_USE_ROOT,
        self::CONFIG_DB_TYPE,
        self::CONFIG_DB_SERVER,
        self::CONFIG_DB_PORT,
        self::CONFIG_DB_USE_SSL,
        self::CONFIG_DB_NAME,
        self::CONFIG_DB_OPTIONS,
        self::CONFIG_DB_TABLE_PREFIX,
        self::CONFIG_DB_TABLE_OPTIONS,
        self::CONFIG_DB_USERNAME,
        self::CONFIG_DB_PASSWORD,
        self::CONFIG_DB_ADMIN_USERNAME,
        self::CONFIG_DB_ADMIN_PASSWORD,
        self::CONFIG_SHARED_DB_NAME,
        self::CONFIG_SHARED_DB_TABLE_PREFIX,
        self::CONFIG_SHARED_DB_TABLES,
        self::CONFIG_MAIN_CACHE_TYPE,
        self::CONFIG_USER_GROUP_RIGHTS,
    );

    public static function getValidConfigOptions() {
        return self::$valid_config_options;
    }
}
