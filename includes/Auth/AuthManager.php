<?php

namespace Isais\Auth;

date_default_timezone_set('UTC');

use Isais\Auth\OaAuthManager;
use Isais\Context\Context;

class AuthManager
{
    private $context;

    private $database;

    private $session_id;

    private $user_id;

    private $username;

    private $token;

    private $expire_timestamp;

    public function __construct(
        $connection_provider,
        $context
    ) {
        $this->context = $context;
        $this->database = $connection_provider->getDatabase();
        $request_cookies = $context->getRequestGetCookies();
        $user_id = null;
        $token = null;
        $current_time = strtotime("now");
        $current_timestamp = gmdate(
            'Y-m-d\TH:i:s\Z',
            $current_time
        );

        $this->database
            ->newQuery(
                'DELETE FROM user_session ' .
                'WHERE us_expire_timestamp < \'' . $current_timestamp . '\';'
            )
            ->fetchResultSet();

        if (
            isset($request_cookies['isais_user']) &&
            isset($request_cookies['isais_token'])
        ) {
            $user_id = $request_cookies['isais_user'];
            $token = $request_cookies['isais_token'];

            $result = $this->database
                ->newQuery(
                    'SELECT ' .
                    'us_id, ' .
                    'us_user_id, ' .
                    'user_id, ' .
                    'user_name_label_id, ' .
                    'lt_label_id, ' .
                    'lt_lang_id, ' .
                    'lt_text, ' .
                    'us_token, ' .
                    'us_expire_timestamp ' .
                    'FROM user_session ' .
                    'INNER JOIN user ' .
                    'ON user_id = us_user_id ' .
                    'INNER JOIN lang_text ' .
                    'ON lt_label_id = user_name_label_id ' .
                    'AND lt_lang_id = 672 ' .
                    'WHERE us_user_id = ' . $user_id . ' ' .
                    'AND us_token = \'' . $token . '\''
                )
                ->fetchResultSet();

            if ($result->hasRows()) {
                $row = $result->fetchRow();
                $expire_time = strtotime($row[8]);

                if ($expire_time >= $current_time) {
                    $this->session_id = $row[0];
                    $this->token = $row[7];
                    $this->user_id = $row[1];
                    $this->username = $row[6];

                    $new_expire_time = strtotime("+31 days");
                    $new_expire_timestamp = gmdate(
                        'Y-m-d\TH:i:s\Z',
                        $new_expire_time
                    );

                    $this->database
                        ->newQuery(
                            'UPDATE user_session ' .
                            'SET us_expire_timestamp = \'' .
                            $new_expire_timestamp .
                            '\'' .
                            'WHERE us_id = ' . $this->session_id . ';'
                        )
                        ->fetchResultSet();

                    $this->expire_timestamp = $new_expire_timestamp;
                }
            } else {
                $new_expire_time = strtotime("+31 days");

                $response_set_cookies = array_merge(
                    $this->context->getResponseSetCookies(),
                    array(
                        'isais_user' => array(
                            'value' => '',
                            'expires' => $new_expire_time,
                            'http_only' => true,
                        ),
                        'isais_token' => array(
                            'value' => '',
                            'expires' => $new_expire_time,
                            'http_only' => true,
                        ),
                    )
                );

                $this->context->setContext(
                    Context::CONTEXT_RESPONSE_SET_COOKIES,
                    $response_set_cookies
                );
            }
        }
    }

    public function isLoggedIn()
    {
        return $this->user_id !== null && $this->token !== null;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getExpireTimestamp()
    {
        return $this->expire_timestamp;
    }

    public function getUsernameByUserId($id)
    {
        $result = $this->database
            ->newQuery(
                'SELECT ' .
                'user_id, ' .
                'user_name_label_id, ' .
                'lt_label_id, ' .
                'lt_lang_id, ' .
                'lt_text ' .
                'FROM user ' .
                'INNER JOIN lang_text ' .
                'ON lt_label_id = user_name_label_id ' .
                'AND lt_lang_id = 672 ' .
                'WHERE user_id = ' . $id . ';'
            )
            ->fetchResultSet();

            if ($result->hasRows()) {
                $row = $result->fetchRow();

                return $row[4];
            }

        return false;
    }

    public function createUser(
        $user_identity_code,
        $user_name_label
    ) {
        $current_time = strtotime("now");
        $created_timestamp = gmdate(
            'Y-m-d\TH:i:s\Z',
            $current_time
        );

        $this->database
            ->newQuery(
                'START TRANSACTION;'
            )
            ->fetchResultSet();
        $this->database
            ->newQuery(
                'INSERT INTO lang_text (' .
                'lt_label_id, ' .
                'lt_lang_id, ' .
                'lt_text' .
                ') ' .
                'VALUES (' .
                '(SELECT MAX(lt.lt_label_id) FROM lang_text AS lt) + 1, ' .
                '672, ' .
                '"' . $user_name_label . '"' .
                ');'
            )
            ->fetchResultSet();
        $this->database
            ->newQuery(
                'INSERT INTO user (' .
                'user_created_timestamp, ' .
                'user_status, ' .
                'user_name_label_id' .
                ') VALUES (' .
                '"' . $created_timestamp . '", ' .
                '1, ' .
                '(SELECT MAX(lt.lt_label_id) FROM lang_text AS lt)' .
                ');'
            )
            ->fetchResultSet();
        $this->database
            ->newQuery(
                'INSERT INTO user_identity (' .
                'ui_user_id, ' .
                'ui_created_timestamp, ' .
                'ui_status, ' .
                'ui_scope, ' .
                'ui_unit_id, ' .
                'ui_code, ' .
                'ui_role' .
                ') VALUES (' .
                'LAST_INSERT_ID(),' .
                '"' . $created_timestamp . '", ' .
                '1, ' .
                '0, ' .
                '0, ' .
                '"' . $user_identity_code . '", ' .
                '0' .
                ');'
            )
            ->fetchResultSet();
        $this->database
            ->newQuery(
                'COMMIT;'
            )
            ->fetchResultSet();
    }

    public function login($user_identity_code, $password)
    {
        $result = OaAuthManager::login($user_identity_code, $password);

        if ($result === false) {
            return false;
        }

        $current_time = strtotime("now");
        $new_expire_time = strtotime("+31 days");
        $created_timestamp = gmdate(
            'Y-m-d\TH:i:s\Z',
            $current_time
        );
        $expire_timestamp = gmdate(
            'Y-m-d\TH:i:s\Z',
            $new_expire_time
        );

        $token = $result['token'];

        $user_name_label = $result['user_name_label'];

        $result = $this->database
            ->newQuery(
                'SELECT ' .
                'ui_user_id, ' .
                'ui_status, ' .
                'ui_code ' .
                'FROM user_identity ' .
                'WHERE ui_status = 1 ' .
                'AND ui_code = \'' . $user_identity_code . '\';'
            )
            ->fetchResultSet();

        if (!$result->hasRows()) {
            $this->createUser($user_identity_code, $user_name_label);

            $result = $this->database
                ->newQuery(
                    'SELECT ' .
                    'ui_user_id, ' .
                    'ui_status, ' .
                    'ui_code ' .
                    'FROM user_identity ' .
                    'WHERE ui_status = 1 ' .
                    'AND ui_code = \'' . $user_identity_code . '\';'
                )
                ->fetchResultSet();
        }

        $row = $result->fetchRow();
        $user_id = $row[0];

        $this->database
            ->newQuery(
                'START TRANSACTION;'
            )
            ->fetchResultSet();
        $this->database
            ->newQuery(
                'INSERT INTO user_session (' .
                'us_created_timestamp, ' .
                'us_user_id, ' .
                'us_token, ' .
                'us_expire_timestamp' .
                ') VALUES (' .
                '"' . $created_timestamp . '", ' .
                $user_id . ', ' .
                '\'' . $token . '\', ' .
                '"' . $expire_timestamp . '"' .
                ');'
            )
            ->fetchResultSet();
        $result = $this->database
            ->newQuery(
                'SELECT ' .
                'us_id, ' .
                'us_created_timestamp, '.
                'us_user_id, ' .
                'user_id, ' .
                'user_name_label_id, ' .
                'lt_label_id, ' .
                'lt_lang_id, ' .
                'lt_text, ' .
                'us_token, ' .
                'us_expire_timestamp ' .
                'FROM user_session ' .
                'INNER JOIN user ' .
                'ON user_id = us_user_id ' .
                'INNER JOIN lang_text ' .
                'ON lt_label_id = user_name_label_id ' .
                'AND lt_lang_id = 672 ' .
                'WHERE us_id = LAST_INSERT_ID();'
            )
            ->fetchResultSet();
        $this->database
            ->newQuery(
                'COMMIT;'
            )
            ->fetchResultSet();

        $row = $result->fetchRow();
        $session_id = $row[0];
        $user_id = $row[2];
        $username = $row[7];
        $token = $row[8];
        $expire_timestamp = $row[9];

        $response_set_cookies = array_merge(
            $this->context->getResponseSetCookies(),
            array(
                'isais_user' => array(
                    'value' => $user_id,
                    'expires' => $new_expire_time,
                    'http_only' => true,
                ),
                'isais_token' => array(
                    'value' => $token,
                    'expires' => $new_expire_time,
                    'http_only' => true,
                ),
            )
        );

        $this->context->setContext(
            Context::CONTEXT_RESPONSE_SET_COOKIES,
            $response_set_cookies
        );

        $this->session_id = $session_id;
        $this->user_id = $user_id;
        $this->username = $username;
        $this->token = $token;
        $this->expire_timestamp = $expire_timestamp;

        return array(
            'user_id' => $user_id,
            'username' => $username,
        );
    }

    public function logout($logout_all = false)
    {
        if ($this->user_id === null) {
            return false;
        }

        $result = $this->database
            ->newQuery(
                'SELECT ' .
                'user_id, ' .
                'user_name_label_id, ' .
                'lt_label_id, ' .
                'lt_lang_id, ' .
                'lt_text ' .
                'FROM user ' .
                'INNER JOIN lang_text ' .
                'ON lt_label_id = user_name_label_id ' .
                'AND lt_lang_id = 672 ' .
                'WHERE user_id = ' . $this->user_id . ';'
            )
            ->fetchResultSet();

        if (!$result->hasRows()) {
            return false;
        }

        $row = $result->fetchRow();
        $user_id = $row[0];
        $username = $row[4];

        $new_expire_time = strtotime("-31 days");

        if ($logout_all === true) {
            $this->database
            ->newQuery(
                'DELETE FROM user_session ' .
                'WHERE us_user_id = ' . $user_id . ';'
            )
            ->fetchResultSet();
        } else {
            $this->database
                ->newQuery(
                    'DELETE FROM user_session ' .
                    'WHERE us_id = ' . $this->session_id . ';'
                )
                ->fetchResultSet();
        }

        $this->session_id = null;
        $this->user_id = null;
        $this->username = null;
        $this->token = null;
        $this->expire_timestamp = null;

        $response_set_cookies = array_merge(
            $this->context->getResponseSetCookies(),
            array(
                'isais_user' => array(
                    'value' => '',
                    'expires' => $new_expire_time,
                    'http_only' => true,
                ),
                'isais_token' => array(
                    'value' => '',
                    'expires' => $new_expire_time,
                    'http_only' => true,
                ),
            )
        );

        $this->context->setContext(
            Context::CONTEXT_RESPONSE_SET_COOKIES,
            $response_set_cookies
        );

        return array(
            'user_id' => $user_id,
            'username' => $username,
        );
    }

    public function logoutAll()
    {
        return $this->logout(true);
    }
}
