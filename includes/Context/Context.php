<?php

namespace Isais\Context;

use Isais\EntryPoint\EntryPoint;

class Context
{
    const CONTEXT_ENTRY_POINT = 'entry_point';

    const CONTEXT_SERVER_PROTOCOL = 'server_protocol';

    const CONTEXT_HTTP_VERSION = 'http_version';

    const CONTEXT_REQUEST_URI = 'request_uri';

    const CONTEXT_URL_PARTS = 'url_parts';

    const CONTEXT_PATH_INFO = 'path_info';

    const CONTEXT_REQUEST_METHOD = 'request_method';

    const CONTEXT_REQUEST_HEADERS = 'request_headers';

    const CONTEXT_REQUEST_DATA = 'request_data';

    const CONTEXT_REQUEST_GET_COOKIES = 'request_get_cookies';

    const CONTEXT_RESPONSE_SET_COOKIES = 'response_set_cookies';

    const CONTEXT_RESPONSE_HEADERS = 'response_headers';

    const CONTEXT_RESPONSE_DATA = 'response_data';

    const CONTEXT_TITLE_FULL_TEXT = 'title_full_text';

    const CONTEXT_USER = 'user';

    private static $valid_contexts = array(
        self::CONTEXT_ENTRY_POINT,
        self::CONTEXT_SERVER_PROTOCOL,
        self::CONTEXT_HTTP_VERSION,
        self::CONTEXT_REQUEST_URI,
        self::CONTEXT_URL_PARTS,
        self::CONTEXT_PATH_INFO,
        self::CONTEXT_REQUEST_METHOD,
        self::CONTEXT_REQUEST_HEADERS,
        self::CONTEXT_REQUEST_DATA,
        self::CONTEXT_REQUEST_GET_COOKIES,
        self::CONTEXT_RESPONSE_SET_COOKIES,
        self::CONTEXT_RESPONSE_HEADERS,
        self::CONTEXT_RESPONSE_DATA,
        self::CONTEXT_TITLE_FULL_TEXT,
        self::CONTEXT_USER,
    );

    private $contexts = array();

    public function __construct()
    {
        if (
            isset($_SERVER['ORIG_PATH_INFO'])
        ) {
            $this->setContext(
                self::CONTEXT_PATH_INFO,
                $_SERVER['ORIG_PATH_INFO']
            );
        } elseif (
            isset($_SERVER['PATH_INFO'])
        ) {
            $this->setContext(
                self::CONTEXT_PATH_INFO,
                $_SERVER['PATH_INFO']
            );
        } else {
            $this->setContext(
                self::CONTEXT_PATH_INFO,
                ''
            );
        }

        $this->setContext(
            self::CONTEXT_TITLE_FULL_TEXT,
            preg_replace(
                '/^\//',
                '',
                $this->getContext(self::CONTEXT_PATH_INFO)
            )
        );

        $this->setContext(
            self::CONTEXT_SERVER_PROTOCOL,
            $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.0' ?
                'HTTP/1.0' :
                'HTTP/1.1'
        );

        $this->setContext(
            self::CONTEXT_HTTP_VERSION,
            str_replace('HTTP/', '', $this->getContext(self::CONTEXT_SERVER_PROTOCOL))
        );

        $this->setContext(
            self::CONTEXT_REQUEST_URI,
            $_SERVER['REQUEST_URI']
        );

        $this->setContext(
            self::CONTEXT_URL_PARTS,
            array(
                // $_SERVER['HTTP_HOST'] with port
                'host' => $_SERVER['HTTP_HOST'],
                // $_SERVER['SERVER_NAME'] without port
                'server' => $_SERVER['SERVER_NAME'],
            )
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
                'post' => $_POST,
                'query' => $_GET,
                'query_and_path' => $_GET,
            )
        );

        $this->setContext(
            self::CONTEXT_REQUEST_GET_COOKIES,
            $_COOKIE
        );

        $this->setContext(
            self::CONTEXT_RESPONSE_SET_COOKIES,
            array(
            )
        );

        $this->setContext(
            self::CONTEXT_RESPONSE_HEADERS,
            array()
        );

        $this->setContext(
            self::CONTEXT_RESPONSE_DATA,
            array()
        );
    }

    public static function hasContext($name)
    {
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
            exit('Invalid context' . $name);
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

    public function getEntryPoint()
    {
        return $this->getContext(self::CONTEXT_ENTRY_POINT);
    }

    public function getRequestUri()
    {
        return $this->getContext(self::CONTEXT_REQUEST_URI);
    }

    public function getServerProtocal()
    {
        return $this->getContext(self::CONTEXT_SERVER_PROTOCOL);
    }

    public function getUrlProtocal() {}

    public function getUrlParts()
    {
        return $this->getContext(self::CONTEXT_URL_PARTS);
    }

    public function getUrlHost()
    {
        $url_parts = $this->getUrlParts();

        return $url_parts['host'];
    }

    public function getUrlServer() {
        $url_parts = $this->getUrlParts();

        return $url_parts['server'];
    }

    public function getUrlPath() {}

    public function getUrlPathInfo()
    {
        return $this->getContext(self::CONTEXT_PATH_INFO);
    }

    public function getRequestMethod()
    {
        return $this->getContext(self::CONTEXT_REQUEST_METHOD);
    }

    public function getRequestHeaders()
    {
        return $this->getContext(self::CONTEXT_REQUEST_HEADERS);
    }

    public function getUrlAllParameters()
    {
        $request_data = $this->getContext(self::CONTEXT_REQUEST_DATA);

        return $request_data['all'];
    }

    public function getUrlPostParameters()
    {
        $request_data = $this->getContext(self::CONTEXT_REQUEST_DATA);

        return $request_data['post'];
    }

    public function getUrlQueryParameters()
    {
        $request_data = $this->getContext(self::CONTEXT_REQUEST_DATA);

        return $request_data['query'];
    }

    public function getUrlQueryAndPathParameters()
    {
        $request_data = $this->getContext(self::CONTEXT_REQUEST_DATA);

        return $request_data['query_and_path'];
    }

    public function getRequestGetCookies()
    {
        return $this->getContext(self::CONTEXT_REQUEST_GET_COOKIES);
    }

    public function getResponseSetCookies()
    {
        return $this->getContext(self::CONTEXT_RESPONSE_SET_COOKIES);
    }

    public function getResponseHeaders()
    {
        return $this->getContext(self::CONTEXT_RESPONSE_HEADERS);
    }

    public function getTitleFullText()
    {
        return $this->getContext(self::CONTEXT_TITLE_FULL_TEXT);
    }
}
