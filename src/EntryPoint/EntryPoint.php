<?php

namespace Isais\EntryPoint;

abstract class EntryPoint
{
    const ENTRY_POINT_API = 'api';

    const ENTRY_POINT_INDEX = 'index';

    const ENTRY_POINT_LOAD = 'load';

    const ENTRY_POINT_MAINTENANCE = 'maintenance';

    public static $valid_entry_points = array(
        self::ENTRY_POINT_API,
        self::ENTRY_POINT_INDEX,
        self::ENTRY_POINT_LOAD,
        self::ENTRY_POINT_MAINTENANCE,
    );

    public function __construct(
    ) {
    }

    public static function hasEntryPoint($entry_point) {
        return in_array($entry_point, self::$valid_entry_points, true);
    }

    abstract public function execute();
}
