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

    protected $services;

    protected $config;

    protected $context;

    public function __construct(
        $service_container
    ) {
        $this->services = $service_container;
        $this->config = $this->services->getConfig();
        $this->context = $this->services->getContext();
    }

    public static function hasEntryPoint($entry_point) {
        return in_array($entry_point, self::$valid_entry_points, true);
    }

    public function setResponseHeaders() {
        foreach ($this->context->getResponseHeaders() as $key => $value) {
            if ($key === 'http_status') {
                header($value);
            } else {
                header($key . ': ' . $value);
            }
        }
    }

    abstract public function execute();
}
