<?php

namespace Isais\EntryPoint;

use Isais\Config\MainConfigNames;
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

    private function setResponseSetCookies() {
        foreach ($this->context->getResponseSetCookies() as $name => $value) {
            if (isset($value['value'])) {
                $expires = (
                    isset($value['expires']) ?
                    $value['expires'] :
                    0
                );
                $path = '/' .
                    $this->config->getOption(MainConfigNames::CONFIG_BASE_DIR);
                $domain = $this->context->getUrlServer();
                $secure = $this->config
                    ->getOption(MainConfigNames::CONFIG_FORCE_HTTPS);
                $http_only = (
                    isset($value['http_only']) ?
                    $value['http_only'] :
                    false
                );

                /**
                 * setcookie(
                 *     string $name,
                 *     string $value = "",
                 *     int $expires_or_options = 0,
                 *     string $path = "",
                 *     string $domain = "",
                 *     bool $secure = false,
                 *     bool $httponly = false
                 * )
                 */
                setcookie(
                    $name,
                    $value['value'],
                    $expires,
                    $path,
                    $domain,
                    $secure,
                    $http_only
                );
            }
        }
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

        $this->setResponseSetCookies();
        parent::setResponseHeaders();

        echo $content;
    }
}
