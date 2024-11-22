<?php

namespace Isais\Title;

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

    private $full_text;

    private $is_main_page = false;

    private $unit = '';

    private $module = '';

    private $item = '';

    public function __construct($full_text)
    {
        $this->full_text = $full_text;
        $this->is_main_page = TitleParser::isMainPage($full_text);
        $this->unit = TitleParser::getUnit($full_text);
        $this->module = TitleParser::getModule($full_text);
        $this->item = TitleParser::getItem($full_text);
    }

    public static function getModules() {
        return self::$available_modules;
    }

    public static function getListModule($module) {
        return self::$available_modules[$module]['list'];
    }

    public static function getItemModule($module) {
        return self::$available_modules[$module]['item'];
    }

    public static function getBaseModulePaths() {
        return self::$base_module_paths;
    }

    public static function getUnitModulePaths() {
        return self::$unit_module_paths;
    }

    public static function hasUnit($unit) {
        return in_array($unit, array_keys(self::getModules()), true);
    }

    public static function hasModule($module) {
        return in_array($module, array_keys(self::getModules()), true);
    }

    public static function hasBaseModulePath($module) {
        return in_array($module, self::getBaseModulePaths(), true);
    }

    public static function hasUnitModulePath($module) {
        return in_array($module, self::getUnitModulePaths(), true);
    }

    public function isMainPage() {
        return $this->is_main_page;
    }

    public function getFullText() {
        return $this->full_text;
    }

    public function getUnit() {
        return $this->unit;
    }

    public function getModule() {
        return $this->module;
    }

    public function getItem() {
        return $this->item;
    }

    public function getContent() {
        return '';
    }
}
