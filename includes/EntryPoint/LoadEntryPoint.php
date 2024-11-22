<?php

namespace Isais\EntryPoint;

use Isais\Context\Context;
use Isais\EntryPoint\EntryPoint;

class LoadEntryPoint extends EntryPoint
{

    private $resource_loader;

    public function __construct($service_container)
    {
        parent::__construct($service_container);

        $this->resource_loader = $this->services->getResourceLoader();
    }

    public function execute()
    {
        $content = $this->resource_loader->getModuleContent();

        $response_headers = array_merge(
            $this->context->getResponseHeaders(),
            array(
                'Cache-Control' => 'public, max-age=300, s-maxage=300, stale-while-revalidate=60',
                'Content-Length' => strlen($content),
                'Content-Type' => $this->resource_loader->getModuleContentType() . '; charset=utf-8',
                'X-Content-Type-Options' => 'nosniff',
            )
        );

        $this->context->setContext(
            Context::CONTEXT_RESPONSE_HEADERS,
            $response_headers
        );

        parent::setResponseHeaders();

        echo $content;
    }
}
