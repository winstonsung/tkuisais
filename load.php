<?php

error_reporting(E_ALL);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/Isais.php';

use Isais\Isais;
use Isais\EntryPoint\EntryPoint;

$isais = new Isais();
$isais->run(EntryPoint::ENTRY_POINT_LOAD);
