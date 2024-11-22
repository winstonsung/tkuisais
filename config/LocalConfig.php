<?php

use Isais\Config\MainConfigNames;

$operating_system = php_uname();

if (strpos($operating_system, 'Windows') !== false) {
	return array();
}

$local_config = array(
	MainConfigNames::CONFIG_DB_PORT => 5432,
	MainConfigNames::CONFIG_DB_USERNAME => '113dbb04',
	MainConfigNames::CONFIG_DB_PASSWORD => '1763-1176',
);

return $local_config;
