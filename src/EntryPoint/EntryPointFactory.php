<?php

namespace Isais\EntryPoint;

use Isais\EntryPoint\ApiEntryPoint;
use Isais\EntryPoint\EntryPoint;
use Isais\EntryPoint\IndexEntryPoint;
use Isais\EntryPoint\LoadEntryPoint;
use Isais\EntryPoint\MaintenanceEntryPoint;

class EntryPointFactory
{
    private static $entry_point_list = array(
        EntryPoint::ENTRY_POINT_API => array(
            'class' => ApiEntryPoint::class,
        ),
        EntryPoint::ENTRY_POINT_INDEX => array(
            'class' => IndexEntryPoint::class,
        ),
        EntryPoint::ENTRY_POINT_LOAD => array(
            'class' => LoadEntryPoint::class,
        ),
        EntryPoint::ENTRY_POINT_MAINTENANCE => array(
            'class' => MaintenanceEntryPoint::class,
        ),
    );

    public static function getEntryPoint(
        $entry_point
    ) {
        if (isset(self::$entry_point_list[$entry_point])) {
            return new self::$entry_point_list[$entry_point]['class']();
        }

        exit('Unknown entry point.');
    }
}
