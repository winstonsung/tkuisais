<?php

use Isais\Config\MainConfigSchema;
use Isais\Config\Config;
use Isais\Context\Context;
use Isais\Data\LanguageData;
use Isais\EntryPoint\EntryPointFactory;
use Isais\Language\LanguageTagFactory;
use Isais\Resource\ResourceLoader;
use Isais\Skin\Skin;
use Isais\Title\Title;
use Isais\User\UserFactory;

/** @phpcs-require-sorted-array */
return array(
    'Config' => function ($service_container) {
        return new Config(
            MainConfigSchema::getConfigSchema(),
            require_once __DIR__ . '/../config/LocalConfig.php'
        );
    },
    'Context' => function ($service_container) {
        return new Context();
    },
    'EntryPoint' => function ($service_container) {
        return EntryPointFactory::getEntryPoint(
            $service_container->getContext()->getEntryPoint(),
            $service_container
        );
    },
    'LanguageData' => function ($service_container) {
        return new LanguageData(
            $service_container->getConfig()
        );
    },
    'LanguageTag' => function ($service_container) {
        return $service_container->getLanguageTagFactory()->getLanguageTag(
                $service_container->getConfig()
            );
    },
    'LanguageTagFactory' => function ($service_container) {
        return new LanguageTagFactory(
            $service_container->getLanguageData(),
            $service_container->getConfig()
        );
    },
    'ResourceLoader' => function ($service_container) {
        return new ResourceLoader(
            $service_container->getContext()
        );
    },
    'Skin' => function ($service_container) {
        return new Skin(
            $service_container->getConfig(),
            $service_container->getContext(),
            $service_container->getResourceLoader(),
            $service_container->getTitle()
        );
    },
    'Title' => function ($service_container) {
        return new Title(
            $service_container->getContext()->getTitleFullText()
        );
    },
    'User' => function ($service_container) {
        return UserFactory::getUser();
    },
);
