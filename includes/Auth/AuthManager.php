<?php

namespace Isais\Auth;

date_default_timezone_set('UTC');

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

        if (
            isset($request_cookies['isais_user']) &&
            isset($request_cookies['isais_token'])
        ) {
            $current_time = strtotime("now");
            $current_timestamp = gmdate(
                'Y-m-d\TH:i:s\Z',
                $current_time
            );
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
            }
        }
    }

    public function isLoggedIn()
    {
        return $this->user_id !== null;
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

    public function create_user($user_identity_code, $token) {
        // $result = $this->database
        // ->newQuery(
            // 'START TRANSACTION; ' .
            // 'INSERT INTO user_session (' .
            // 'us_created_timestamp, '.
            // 'us_user_id, ' .
            // 'us_token, ' .
            // 'us_expire_timestamp' .
            // ') VALUES (' .
            // '\'' . $created_timestamp . '\', ' .
            // $user_id . ', ' .
            // '\'' . $token . '\', ' .
            // '\'' . $expire_timestamp . '\'' .
            // '); ' .
            // 'WHERE us_id = LAST_INSERT_ID(); ' .
            // 'COMMIT;'
        // )
        // ->fetchResultSet();
    }

    public function login($user_identity_code, $token)
    {
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
            $this->create_user($user_identity_code, $token);

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

        $result = $this->database
            ->newQuery(
                'SELECT ' .
                'us_user_id, ' .
                'us_token ' .
                'FROM user_session ' .
                'WHERE us_user_id = \'' . $user_id . '\' ' .
                'AND us_token = \'' . $token . '\';'
            )
            ->fetchResultSet();

        if ($result->hasRows()) {
            $row = $result->fetchRow();
            $user_id = $row[0];
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

            $this->database
                ->newQuery(
                    'UPDATE user_session ' .
                    'SET us_expire_timestamp = \'' .
                    $expire_timestamp .
                    '\' ' .
                    'WHERE us_user_id = \'' . $user_id . '\' ' .
                    'AND us_token = \'' . $token . '\';'
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
                    'WHERE us_user_id = \'' . $user_id . '\' ' .
                    'AND us_token = \'' . $token . '\';'
                )
                ->fetchResultSet();

            if ($result->hasRows()) {
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

                return true;
            }
        }

        return false;
    }

    public function logout()
    {
        $new_expire_time = strtotime("-31 days");
        $expire_timestamp = gmdate(
            'Y-m-d\TH:i:s\Z',
            $new_expire_time
        );
        $this->database
        ->newQuery(
            'UPDATE user_session ' .
            'SET us_expire_timestamp = \'' .
            $expire_timestamp .
            '\' ' .
            'WHERE us_user_id = \'' . $user_id . '\' ' .
            'AND us_token = \'' . $token . '\';'
        )
        ->fetchResultSet();

        $this->session_id = null;
        $this->user_id = null;
        $this->username = null;
        $this->token = null;
        $this->expire_timestamp = null;
    }

    public function logoutAll()
    {
        $this->database
        ->newQuery(
            'DELETE FROM user_session' .
            'WHERE us_user_id = ' .
            $this->user_id .
            ';'
        )
        ->fetchResultSet();

        $this->session_id = null;
        $this->user_id = null;
        $this->username = null;
        $this->token = null;
        $this->expire_timestamp = null;
    }
}
