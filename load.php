<?php

error_reporting( -1 );
ini_set( 'display_startup_errors', 1 );
ini_set( 'display_errors', 1 );

require_once __DIR__ . '/src/Isais.php';

use Isais\Isais;
use Isais\EntryPoint\EntryPoint;

(new Isais())->run(EntryPoint::ENTRY_POINT_LOAD);
