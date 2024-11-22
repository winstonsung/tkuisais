<?php

require_once __DIR__ . '/../src/EntryPoint.php';

use Isais\EntryPoint;

(new EntryPoint(
    EntryPoint::ENTRY_POINT_MAINTENANCE
))->run();
