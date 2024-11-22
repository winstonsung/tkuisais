<?php

namespace Isais\Resource;

use Isais\Context\Context;

class ResourceLoader {
    private static $style_modules = array(
        'codex',
        'skin',
    );

    private static $script_modules = array(
        'skin',
    );

    private $context;

    public function __construct(
        $context
    ) {
        $this->context = $context;
    }

    public function getModules($type) {
        if ($type === 'styles') {
            return self::$style_modules;
        }

        if ($type === 'scripts') {
            return self::$script_modules;
        }

        exit('Invalid type patameter');
    }

    public function getModulesParameter($type) {
        if ($type === 'styles') {
            return implode('|', self::$style_modules);
        }

        if ($type === 'scripts') {
            return implode('|', self::$script_modules);
        }

        exit('Invalid type patameter');
    }

    public function getModuleContentType() {
        $params = $this->context->getUrlAllParameters();

        if (!isset($params['type'])) {
            exit('Missing require type parameter');
        }

        $type = $params['type'];

        if ($type === 'styles') {
            return 'text/css';
        }

        if ($type === 'scripts') {
            return 'text/javascript';
        }

        exit('Invalid type patameter');
    }

    public function getModuleContent() {
        $params = $this->context->getUrlAllParameters();

        if (!isset($params['type'])) {
            exit('Missing require type parameter');
        }

        if (!isset($params['modules'])) {
            exit('Missing require type modules');
        }

        $type = $params['type'];
        $modules = explode('|', $params['modules']);

        if ($type === 'styles') {
            $content = '';

            foreach ($modules as $module) {
                if (in_array($module, self::$style_modules, true)) {
                    $content .= file_get_contents(
                        __DIR__ . '/../../resources/styles/' . $module . '.css'
                    );
                } else {
                    $content .= '/** Unknown module \'' . $module . '\' */';
                }
            }

            return $content;
        }

        if ($type === 'scripts') {
            $content = '';

            foreach ($modules as $module) {
                if (in_array($module, self::$script_modules, true)) {
                    $content .= file_get_contents(
                        __DIR__ . '/../../resources/scripts/' . $module . '.js'
                    );
                } else {
                    $content .= '/** Unknown module \'' . $module . '\' */';
                }
            }

            return $content;
        }

        exit('Invalid type patameter');
    }
}
