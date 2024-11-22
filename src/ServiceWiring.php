<?php

use Isais\Config\MainConfigSchema;
use Isais\Config\Config;
use Isais\Config\MainConfigNames;
use Isais\Context\Context;
use Isais\EntryPoint\EntryPointFactory;
use Isais\Language\LanguageTagFactory;
use Isais\User\UserFactory;

/** @phpcs-require-sorted-array */
return array(
    'Config' => static function ($service_container) {
        return new Config(
            MainConfigSchema::getConfigSchema(),
            require_once __DIR__ . '/../config/LocalConfig.php'
        );
    },
    'Context' => static function ($service_container) {
        return new Context();
    },
    'EntryPoint' => static function ($service_container) {
        return EntryPointFactory::getEntryPoint(
            $service_container->getContext()->get(Context::CONTEXT_ENTRY_POINT)
        );
    },
    'LanguageTag' => static function ($service_container) {
        return $service_container->getLanguageTagFactory()
            ->getLanguageTag(
                $service_container->getConfig()
            );
    },
    'LanguageTagFactory' => static function ($service_container) {
        return new LanguageTagFactory(
            $service_container->getConfig()
                ->getOption(MainConfigNames::CONFIG_SITE_LANGUAGE)
        );
    },
    'User' => static function ($service_container) {
        return UserFactory::getUser();
    },
);
