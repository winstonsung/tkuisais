<?php

namespace Isais\Content;

use Isais\Config\MainConfigNames;
use Isais\Content\Content;

class UserLoginContent extends Content
{
    public function __construct($config, $connection_provider, $context, $title)
    {
        parent::__construct($config, $connection_provider, $context, $title);
        $params_all = $this->context->getUrlAllParameters();
        $params_post = $this->context->getUrlPostParameters();

        if (
            isset($params_post['user']) &&
            isset($params_post['password'])
        ) {
            $user_identity_code = $params_post['user'];

            $result = $this->connection_provider->getDatabase()
                ->newQuery(
                    'SELECT ' .
                    'ui_user_id, ' .
                    'ui_status, ' .
                    'ui_code ' .
                    'FROM user_identity ' .
                    'WHERE ui_status = 1 ' .
                    'AND ui_code = \'' . $user_identity_code . '\''
                )
                ->fetchResultSet();

            if ($result->hasRows()) {

            } else {

            }
        }

        $this->display_title = '登入';
        $this->display_content = '<p>' .
            '要登入' .
            $this->config
                ->getOption(MainConfigNames::CONFIG_SITE_CANONICAL_NAME) .
            '，' .
            '您必須啟用' .
            '<a href="https://zh.wikipedia.org/zh-tw/Cookie" title="cookie">cookie</a>' .
            '功能。'.
            '</p>' .
            '<div id="User_login_form" class="cdx-form-wrapper">' .
            '<form class="cdx-form cdx-form--width-limited" method="post" action="' .
            $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
            $this->context->getUrlHost() .
            '/' .
            str_replace(
                '$1',
                'login/' . (
                    (
                        isset($params_all['returnto']) &&
                        $params_all['returnto'] !== ''
                    ) ?
                    '?returnto=' . $params_all['returnto'] :
                    ''
                ),
                $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
            ) .
            '">' .
            '<div class="cdx-field">' .
            '<div class="cdx-label">' .
            '<label class="cdx-label__label" for="user">' .
            '<span class="cdx-label__label__text">' .
            '帳號' .
            '</span>' .
            '</label>' .
            '<span class="cdx-label__description">' .
            '學號/人員代號' .
            '</span>' .
            '</div>' .
            '<div class="cdx-field__control">' .
            '<div class="cdx-text-input">' .
            '<input ' .
            'id="user" ' .
            'name="user" ' .
            'size="20" ' .
            'placeholder="請輸入您的帳號" ' .
            'class="cdx-text-input__input" ' .
            'tabindex="1" ' .
            'required="" ' .
            'autofocus="" ' .
            'autocomplete="username"' .
            '>' .
            '</div>' .
            '</div>' .
            // '<div class="cdx-field__help-text">' .
            // '</div>' .
            '</div>' .
            '<div class="cdx-field">' .
            '<div class="cdx-label">' .
            '<label class="cdx-label__label" for="password">' .
            '<span class="cdx-label__label__text">' .
            '密碼' .
            '</span>' .
            '</label>' .
            '</div>' .
            '<div class="cdx-field__control">' .
            '<div class="cdx-text-input">' .
            '<input ' .
            'id="password" ' .
            'name="password" ' .
            'size="20" ' .
            'placeholder="輸入您的密碼" ' .
            'class="cdx-text-input__input" ' .
            'tabindex="2" ' .
            'required="" ' .
            'autocomplete="current-password" ' .
            'type="password"' .
            '>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '<div class="cdx-field">' .
            '<div class="cdx-field__control">' .
            '<button class="' .
            'cdx-button ' .
            'cdx-button--weight-primary ' .
            'cdx-button--action-progressive ' .
            'cdx-button--width-full' .
            '" type="submit" ' .
            'tabindex="6"' .
            '>' .
            '登入' .
            '</button>' .
            '</div>' .
            '</div>' .
            '</form>' .
            '</div>';
    }
}
