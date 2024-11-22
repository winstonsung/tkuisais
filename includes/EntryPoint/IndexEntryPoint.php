<?php

namespace Isais\EntryPoint;

use Isais\Context\Context;
use Isais\EntryPoint\EntryPoint;

class IndexEntryPoint extends EntryPoint
{
    private $title;

    public function __construct($service_container)
    {
        parent::__construct($service_container);

        $this->title = $this->services->getTitle();
    }

    public function execute()
    {
        $content = $this->services->getSkin()->getHtml();

        $response_headers = array_merge(
            $this->context->getResponseHeaders(),
            array(
            'Cache-Control' => 'private, must-revalidate, max-age=0',
            'Content-Length' => strlen($content),
            'Content-Type' => 'text/html; charset=utf-8',
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
