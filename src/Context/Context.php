<?php

namespace Isais\Context;

use Isais\EntryPoint\EntryPoint;

class Context
{
    const CONTEXT_ENTRY_POINT = 'entry_point';

    const CONTEXT_URL_PARTS = 'url_parts';

    const CONTEXT_REQUEST_TIME = 'request_time';

    const CONTEXT_REQUEST_METHOD = 'request_method';

    const CONTEXT_REQUEST_HEADERS = 'request_headers';

    const CONTEXT_REQUEST_DATA = 'request_data';

    const CONTEXT_RESPONSE_HEADERS = 'response_headers';

    const CONTEXT_RESPONSE_DATA = 'response_data';

    private static $valid_contexts = array(
        self::CONTEXT_ENTRY_POINT,
        self::CONTEXT_URL_PARTS,
        self::CONTEXT_REQUEST_TIME,
        self::CONTEXT_REQUEST_METHOD,
        self::CONTEXT_REQUEST_HEADERS,
        self::CONTEXT_REQUEST_DATA,
        self::CONTEXT_RESPONSE_HEADERS,
    );

    private $contexts = array();

    public function __construct(
    ) {
        $this->setContext(
            self::CONTEXT_URL_PARTS,
            array(
                'host' => $_SERVER['HTTP_HOST'],
            )
        );

        $this->setContext(
            self::CONTEXT_REQUEST_TIME,
            $_SERVER['REQUEST_TIME_FLOAT']
        );

        $this->setContext(
            self::CONTEXT_REQUEST_METHOD,
            $_SERVER['REQUEST_METHOD']
        );

        $this->setContext(
            self::CONTEXT_REQUEST_HEADERS,
            array(
                'Accept-Language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'],
            )
        );

        // POST overrides GET data
        // We don't use $_REQUEST here to avoid interference from cookies.
        $this->setContext(
            self::CONTEXT_REQUEST_DATA,
            array(
                'all' => $_POST + $_GET,
                'query' => $_GET,
                'query_and_path' => $_GET,
            )
        );

        $this->setContext(
            self::CONTEXT_RESPONSE_DATA,
            array()
        );
    }

    public static function hasContext($name) {
        return in_array($name, self::$valid_contexts, true);
    }

    public function getContext(
        $name
    ) {
        if (isset($this->contexts[$name])) {
            return $this->contexts[$name];
        }

        return null;
    }

    public function setContext(
        $name,
        $value
    ) {
        if (!self::hasContext($name)) {
            exit('Invalid context');
        }

        if (
            $name === self::CONTEXT_ENTRY_POINT &&
            !EntryPoint::hasEntryPoint($value)
        ) {
            exit('Unknown entrypoint context');
        }

        $this->contexts[$name] = $value;
    }

    /** Context helper functions */

    public function getEntryPoint() {
        return $this->getContext(self::CONTEXT_ENTRY_POINT);
    }

    public function getRequestProtocal() {}

    public function getUrlProtocal() {}

    public function getUrlServer() {}

    public function getUrlHost()
    {
        return $this->getContext(self::CONTEXT_URL_PARTS)['host'];
    }

    public function getUrlPath() {}

    public function getRequestMethod()
    {
        return $this->getContext(self::CONTEXT_REQUEST_METHOD);
    }

    public function getRequestHeaders()
    {
        return $this->getContext(self::CONTEXT_REQUEST_HEADERS);
    }

    public function getUrlAllParameters() {}

    public function getUrlQueryParameters() {}

    public function getUrlQueryAndPathParameters() {}

    public function getResponseHeaders()
    {
        return $this->getContext(self::CONTEXT_RESPONSE_HEADERS);
    }
}
