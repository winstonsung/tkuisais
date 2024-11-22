<?php

namespace Isais\Title;

use Isais\Title\Title;

class TitleParser {
    public static function isMainPage($full_text) {
        if ($full_text === '') {
            return true;
        }

        return false;
    }

    public static function getUnit($full_text) {
        if ($full_text !== '') {
            $parts = explode('/', $full_text);

            if (!Title::hasBaseModulePath($parts[0])) {
                return $parts[0];
            }
        }

        return '';
    }

    public static function getModule($full_text) {
        if ($full_text !== '') {
            $parts = explode('/', $full_text);

            if (Title::hasBaseModulePath($parts[0])) {
                if (isset($parts[1]) && $parts[1] !== '') {
                    return Title::getItemModule($parts[0]);
                }

                return Title::getListModule($parts[0]);
            }

            array_shift($parts);

            if (
                isset($parts[0]) &&
                Title::hasUnitModulePath($parts[0])
            ) {
                if (isset($parts[1]) && $parts[1] !== '') {
                    return Title::getItemModule($parts[0]);
                }

                return Title::getListModule($parts[0]);
            }

            return Title::getListModule('page');
        }

        return '';
    }

    public static function getItem($full_text) {
        if ($full_text !== '') {
            $parts = explode('/', $full_text);

            if (Title::hasBaseModulePath($parts[0])) {
                array_shift($parts);

                if (isset($parts[0]) && $parts[0] !== '') {
                    return implode('/', $parts);
                }

                return '';
            }

            array_shift($parts);

            if (
                isset($parts[0]) &&
                Title::hasUnitModulePath($parts[0])
            ) {
                array_shift($parts);

                if (isset($parts[0]) && $parts[0] !== '') {
                    return implode('/', $parts);
                }

                return '';
            }

            return implode('/', $parts);
        }

        return '';
    }
}
