<?php

namespace Isais\Title;

use Isais\Context\Context;
use Isais\Title\TitleParser;

class Title {
    private static $available_modules = array(
        'activity' => array(
            'list' => 'Isais\\Content\\ActivityListContent',
            'item' => 'Isais\\Content\\ActivityContent',
        ),
        'announcement' => array(
            'list' => 'Isais\\Content\\AnnouncementListContent',
            'item' => 'Isais\\Content\\AnnouncementContent',
        ),
        'appeal' => array(
            'list' => 'Isais\\Content\\AppealListContent',
            'item' => 'Isais\\Content\\AppealContent',
        ),
        'bank' => array(
            'list' => 'Isais\\Content\\BankListContent',
            'item' => 'Isais\\Content\\BankContent',
        ),
        'club' => array(
            'list' => 'Isais\\Content\\ClubListContent',
            'item' => 'Isais\\Content\\ClubContent',
        ),
        'course' => array(
            'list' => 'Isais\\Content\\CourseListContent',
            'item' => 'Isais\\Content\\CourseContent',
        ),
        'login' => array(
            'item' => 'Isais\\Content\\UserLoginContent',
        ),
        'logout' => array(
            'item' => 'Isais\\Content\\UserLogoutContent',
        ),
        'main_page' => array(
            'item' => 'Isais\\Content\\MainPageContent',
        ),
        'not_found' => array(
            'item' => 'Isais\\Content\\NotFoundContent',
        ),
        'page' => array(
            'list' => 'Isais\\Content\\WikiPageListContent',
            'item' => 'Isais\\Content\\WikiPageContent',
        ),
        'report' => array(
            'list' => 'Isais\\Content\\ReportListContent',
            'item' => 'Isais\\Content\\ReportContent',
        ),
        'unit' => array(
            'list' => 'Isais\\Content\\UnitListContent',
            'item' => 'Isais\\Content\\UnitContent',
        ),
        'user' => array(
            'list' => 'Isais\\Content\\UserListContent',
            'item' => 'Isais\\Content\\UserContent',
        ),
        'user_group' => array(
            'list' => 'Isais\\Content\\UserGroupListContent',
            'item' => 'Isais\\Content\\UserGroupContent',
        ),
    );

    private static $base_module_paths = array(
        'activity',
        'announcement',
        'appeal',
        'bank',
        'club',
        'course',
        'login',
        'logout',
        'page',
        'report',
        'unit',
        'user',
        'user_group',
    );

    private static $unit_module_paths = array(
        'activity',
        'announcement',
        'page',
    );

    private $auth_manager;

    private $config;

    private $connection_provider;

    private $content;

    private $context;

    private $full_text;

    private $is_main_page = false;

    private $is_not_found_page = false;

    private $creatable_unit = '';

    private $creatable_club = '';

    private $creatable_unit_creatable_item = '';

    private $creatable_club_creatable_item = '';

    private $unit_type = '';

    private $unit = '';

    private $unit_id = '';

    private $unit_name = '';

    private $module = '';

    private $item = '';

    public function __construct(
        $auth_manager,
        $config,
        $connection_provider,
        $context
    ) {
        $this->auth_manager = $auth_manager;
        $this->config = $config;
        $this->connection_provider = $connection_provider;
        $this->context = $context;

        $full_text = $context->getTitleFullText();
        $this->full_text = $full_text;
        $this->is_main_page = TitleParser::isMainPage($full_text);
        $this->creatable_unit = TitleParser::getCreatableUnit(
            $full_text,
            $connection_provider
        );
        $this->creatable_club = TitleParser::getCreatableClub(
            $full_text,
            $connection_provider
        );
        $this->creatable_unit_creatable_item = TitleParser::getCreatableUnitCreatableItem(
            $full_text,
            $connection_provider
        );
        $this->creatable_club_creatable_item = TitleParser::getCreatableClubCreatableItem(
            $full_text,
            $connection_provider
        );
        $this->unit_type = TitleParser::getUnitType($full_text, $connection_provider);
        $this->unit = TitleParser::getUnit($full_text, $connection_provider);
        $this->module = TitleParser::getModule($full_text, $connection_provider);
        $this->item = TitleParser::getItem($full_text, $connection_provider);

        if ($this->unit !== '') {
            $result = $connection_provider
                ->getDatabase()
                ->newQuery(
                    'SELECT ' .
                    'unit_id, ' .
                    'unit_code, ' .
                    'unit_name_label_id, ' .
                    'lt_name.lt_label_id, ' .
                    'lt_name.lt_lang_id, ' .
                    'lt_name.lt_text ' .
                    'FROM unit ' .
                    'INNER JOIN lang_text AS lt_name ' .
                    'ON lt_name.lt_label_id = unit_name_label_id ' .
                    'AND lt_name.lt_lang_id = 672 ' .
                    'WHERE unit_code = \'' . $this->unit . '\''
                )
                ->fetchResultSet();

            if ($result->hasRows()) {
                $row = $result->fetchRow();
                $this->unit_id = $row[0];
                $this->unit_name = $row[5];
            }
        }

        $content_class = $this->module;
        $this->content = new $content_class(
            $this->auth_manager,
            $this->config,
            $this->connection_provider,
            $this->context,
            $this
        );

        if ($this->content->showNotFoundPage()) {
            $this->is_not_found_page = true;
            $this->module = $this->getItemModule('not_found');
            $content_class = $this->module;
            $this->content = new $content_class(
                $this->auth_manager,
                $this->config,
                $this->connection_provider,
                $this->context,
                $this
            );
        }

        $response_headers = $this->context->getResponseHeaders();

        if (
            !isset($response_headers['http_status']) &&
            !$this->content->hasContent()
        ) {
            $response_headers = array_merge(
                $response_headers,
                array(
                    'http_status' => $context->getServerProtocal() . ' 404 Not Found',
                )
            );

            $this->context->setContext(
                Context::CONTEXT_RESPONSE_HEADERS,
                $response_headers
            );
        }
    }

    public static function getModules()
    {
        return self::$available_modules;
    }

    public static function getListModule($module)
    {
        return self::$available_modules[$module]['list'];
    }

    public static function getItemModule($module)
    {
        return self::$available_modules[$module]['item'];
    }

    public static function getBaseModulePaths()
    {
        return self::$base_module_paths;
    }

    public static function getUnitModulePaths()
    {
        return self::$unit_module_paths;
    }

    public static function hasUnit($unit)
    {
        return in_array($unit, array_keys(self::getModules()), true);
    }

    public static function hasModule($module)
    {
        return in_array($module, array_keys(self::getModules()), true);
    }

    public static function hasBaseModulePath($module)
    {
        return in_array($module, self::getBaseModulePaths(), true);
    }

    public static function hasUnitModulePath($module)
    {
        return in_array($module, self::getUnitModulePaths(), true);
    }

    public function isMainPage()
    {
        return $this->is_main_page;
    }

    public function getFullText()
    {
        return $this->full_text;
    }

    public function getCreatableUnit()
    {
        return $this->creatable_unit;
    }

    public function getCreatableClub()
    {
        return $this->creatable_club;
    }

    public function getUnitType()
    {
        return $this->unit_type;
    }

    public function getUnit()
    {
        return $this->unit;
    }

    public function getUnitId()
    {
        return $this->unit_id;
    }

    public function getUnitName()
    {
        return $this->unit_name;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getCreatableUnitCreatableItem()
    {
        return $this->creatable_unit_creatable_item;
    }

    public function getCreatableClubCreatableItem()
    {
        return $this->creatable_club_creatable_item;
    }

    public function getItem()
    {
        return $this->item;
    }

    public function isNotFoundPage()
    {
        return $this->is_not_found_page;
    }

    public function hasContent()
    {
        return $this->content->hasContent();
    }

    public function getContent()
    {
        return $this->content;
    }
}
