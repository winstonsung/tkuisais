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

    public static function hasUnit($unit_code, $connection_provider) {
        if (
            !Title::hasBaseModulePath($unit_code) &&
            $connection_provider
                ->getDatabase()
                ->newQuery(
                    'SELECT ' .
                    'unit_type, ' .
                    'unit_code ' .
                    'FROM unit ' .
                    'WHERE unit_type = 1 ' .
                    'AND unit_code = \'' . $unit_code . '\''
                )
                ->fetchResultSet()
                ->hasRows()
        ) {
            return true;
        }

        return false;
    }

    public static function hasClub($club_code, $connection_provider) {
        if (
            $connection_provider
                ->getDatabase()
                ->newQuery(
                    'SELECT ' .
                    'unit_type, ' .
                    'unit_code ' .
                    'FROM unit ' .
                    'WHERE unit_type = 2 ' .
                    'AND unit_code = \'' . $club_code . '\''
                )
                ->fetchResultSet()
                ->hasRows()
        ) {
            return true;
        }

        return false;
    }

    public static function getCreatableUnit($full_text, $connection_provider) {
        if ($full_text !== '') {
            $parts = explode('/', $full_text);

            if (end($parts) === '') {
                array_pop($parts);
            }

            if (
                !Title::hasBaseModulePath($parts[0]) &&
                !self::hasUnit($parts[0], $connection_provider)
            ) {
                return $parts[0];
            }
        }

        return '';
    }

    public static function getCreatableClub($full_text, $connection_provider) {
        if ($full_text !== '') {
            $parts = explode('/', $full_text);

            if (end($parts) === '') {
                array_pop($parts);
            }

            if (
                isset($parts[0]) &&
                $parts[0] === 'club' &&
                isset($parts[1]) &&
                !self::hasClub($parts[1], $connection_provider)
            ) {
                return $parts[1];
            }
        }

        return '';
    }

    public static function getCreatableUnitCreatableItem($full_text, $connection_provider) {
        if ($full_text !== '') {
            $parts = explode('/', $full_text);

            if (end($parts) === '') {
                array_pop($parts);
            }

            if (
                !Title::hasBaseModulePath($parts[0]) &&
                !self::hasUnit($parts[0], $connection_provider) &&
                isset($parts[1])
            ) {
                array_shift($parts);

                return implode('/', $parts);
            }
        }

        return '';
    }

    public static function getCreatableClubCreatableItem($full_text, $connection_provider) {
        if ($full_text !== '') {
            $parts = explode('/', $full_text);

            if (end($parts) === '') {
                array_pop($parts);
            }

            if (
                isset($parts[0]) &&
                $parts[0] === 'club' &&
                isset($parts[1]) &&
                !self::hasClub($parts[1], $connection_provider) &&
                isset($parts[2])
            ) {
                array_shift($parts);
                array_shift($parts);

                return implode('/', $parts);
            }
        }

        return '';
    }

    public static function getUnitType($full_text, $connection_provider) {
        if ($full_text !== '') {
            $parts = explode('/', $full_text);

            if (end($parts) === '') {
                array_pop($parts);
            }

            if (
                !Title::hasBaseModulePath($parts[0]) &&
                self::hasUnit($parts[0], $connection_provider)
            ) {
                return 'unit';
            }

            if (
                $parts[0] === 'club' &&
                isset($parts[1]) &&
                self::hasClub($parts[1], $connection_provider)
            ) {
                return 'club';
            }
        }

        return '';
    }

    public static function getUnit($full_text, $connection_provider) {
        if ($full_text !== '') {
            $parts = explode('/', $full_text);

            if (end($parts) === '') {
                array_pop($parts);
            }

            if (
                !Title::hasBaseModulePath($parts[0]) &&
                self::hasUnit($parts[0], $connection_provider)
            ) {
                return $parts[0];
            }

            if (
                $parts[0] === 'club' &&
                isset($parts[1]) &&
                self::hasClub($parts[1], $connection_provider)
            ) {
                return $parts[1];
            }
        }

        return '';
    }

    public static function getModule($full_text, $connection_provider) {
        if ($full_text !== '') {
            $parts = explode('/', $full_text);

            if (end($parts) === '') {
                array_pop($parts);
            }

            if (in_array($parts[0], array('page', 'unit'))) {
                return Title::getListModule($parts[0]);
            }

            if (in_array($parts[0], array('login', 'logout'))) {
                return Title::getItemModule($parts[0]);
            }

            if ($parts[0] === 'club') {
                array_shift($parts);

                if (!isset($parts[0])) {
                    return Title::getListModule('club');
                }

                if (self::hasClub($parts[0], $connection_provider)) {
                    if (!isset($parts[1])) {
                        return Title::getItemModule('club');
                    }

                    array_shift($parts);

                    if ($parts[0] === 'page') {
                        return Title::getListModule('page');
                    }

                    if (Title::hasUnitModulePath($parts[0])) {
                        if (isset($parts[1]) && $parts[1] !== '') {
                            return Title::getItemModule($parts[0]);
                        }

                        return Title::getListModule($parts[0]);
                    }
                }
            }

            if (Title::hasBaseModulePath($parts[0])) {
                if (isset($parts[1]) && $parts[1] !== '') {
                    return Title::getItemModule($parts[0]);
                }

                return Title::getListModule($parts[0]);
            }

            if (self::hasUnit($parts[0], $connection_provider)) {
                if (!isset($parts[1])) {
                    return Title::getItemModule('unit');
                }

                array_shift($parts);

                if ($parts[0] === 'page') {
                    return Title::getListModule('page');
                }

                if (Title::hasUnitModulePath($parts[0])) {
                    if (isset($parts[1]) && $parts[1] !== '') {
                        return Title::getItemModule($parts[0]);
                    }

                    return Title::getListModule($parts[0]);
                }
            }

            return Title::getItemModule('page');
        }

        return Title::getItemModule('main_page');
    }

    public static function getItem($full_text, $connection_provider) {
        if ($full_text !== '') {
            $parts = explode('/', $full_text);

            if (end($parts) === '') {
                array_pop($parts);
            }

            if (in_array($parts[0], array('login', 'logout'))) {
                return '';
            }

            if ($parts[0] === 'club') {
                if (!isset($parts[1])) {
                    return $parts[0];
                }

                array_shift($parts);

                if (self::hasClub($parts[0], $connection_provider)) {
                    if (!isset($parts[1])) {
                        return $parts[0];
                    }

                    array_shift($parts);

                    if ($parts[0] === 'page') {
                        return '';
                    }

                    if (Title::hasUnitModulePath($parts[0])) {
                        array_shift($parts);

                        if (isset($parts[0]) && $parts[0] !== '') {
                            return implode('/', $parts);
                        }

                        return '';
                    }
                }
            }

            if (Title::hasBaseModulePath($parts[0])) {
                if ($parts[0] === 'page') {
                    return '';
                }

                array_shift($parts);

                if (isset($parts[0]) && $parts[0] !== '') {
                    return implode('/', $parts);
                }

                return '';
            }

            if (self::hasUnit($parts[0], $connection_provider)) {
                if (!isset($parts[1])) {
                    return $parts[0];
                }

                array_shift($parts);

                if ($parts[0] === 'page') {
                    return '';
                }

                if (Title::hasUnitModulePath($parts[0])) {
                    array_shift($parts);

                    if (isset($parts[0]) && $parts[0] !== '') {
                        return implode('/', $parts);
                    }

                    return '';
                }
            }

            return implode('/', $parts);
        }

        return '';
    }
}
