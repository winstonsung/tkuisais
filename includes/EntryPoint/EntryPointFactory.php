<?php

namespace Isais\EntryPoint;

class EntryPointFactory
{
    private static $entry_point_list = array(
        EntryPoint::ENTRY_POINT_API => array(
            'class' => 'Isais\EntryPoint\ApiEntryPoint',
        ),
        EntryPoint::ENTRY_POINT_INDEX => array(
            'class' => 'Isais\EntryPoint\IndexEntryPoint',
        ),
        EntryPoint::ENTRY_POINT_LOAD => array(
            'class' => 'Isais\EntryPoint\LoadEntryPoint',
        ),
        EntryPoint::ENTRY_POINT_MAINTENANCE => array(
            'class' => 'Isais\EntryPoint\MaintenanceEntryPoint',
        ),
    );

    public static function getEntryPoint(
        $entry_point,
        $service_container
    ) {
        if (isset(self::$entry_point_list[$entry_point])) {
            return new self::$entry_point_list[$entry_point]['class'](
                $service_container
            );
        }

        exit('Unknown entry point.');
    }
}
