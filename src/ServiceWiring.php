<?php

use Isais\Config\Config;
use Isais\Context\Context;
use Isais\EntryPoint\EntryPointFactory;
use Isais\Language\LanguageFactory;
use Isais\User\UserFactory;

/** @phpcs-require-sorted-array */
return array(
    'Config' => static function ($service_container) {
        return new Config(
            require_once __DIR__ . '/../DefaultConfig.php',
            require_once __DIR__ . '/../../config_local.php'
        );
    },
    'Context' => static function ($service_container) {
        return new Context();
    },
    'EntryPoint' => static function ($service_container) {
        return EntryPointFactory::getEntryPoint(
            $service_container->getContext()->get( 'entry_point' )
        );
    },
    'Language' => static function ($service_container) {
        return LanguageFactory::getLanguage(
            $service_container->getContext()
        );
    },
    'User' => static function ($service_container) {
        return UserFactory::getUser();
    },
);
