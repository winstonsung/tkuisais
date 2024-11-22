<?php

namespace Isais\Content;

use Isais\Config\MainConfigNames;
use Isais\Content\Content;
use Isais\Context\Context;

class UserLoginContent extends Content
{
    public function __construct($auth_manager, $config, $connection_provider, $context, $title)
    {
        parent::__construct($auth_manager, $config, $connection_provider, $context, $title);

        $params_all = $this->context->getUrlAllParameters();
        $params_post = $this->context->getUrlPostParameters();

        if (
            isset($params_post['user']) &&
            isset($params_post['password'])
        ) {
            $user_identity_code = $params_post['user'];
            $password = $params_post['password'];
            $result = $this->auth_manager->login($user_identity_code, $password);

            if ($result !== false) {
                $response_headers = array_merge(
                    $this->context->getResponseHeaders(),
                    array(
                        'http_status' => $context->getServerProtocal() . ' 302 Found',
                        'location' => $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                            $this->context->getUrlHost() .
                            '/' .
                            str_replace(
                                '$1',
                                (
                                    isset($params_all['returnto']) ?
                                    trim($params_all['returnto']) :
                                    ''
                                ),
                                $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
                            ),
                    )
                );

                $this->context->setContext(
                    Context::CONTEXT_RESPONSE_HEADERS,
                    $response_headers
                );

                $username = $result['username'];

                $this->display_title = '已登入';
                $this->display_content = '<p>已登入使用者「' . $username . '」。</p>' .
                    '<p>正在重新導向……</p>';

                return;
            } else {
                $this->display_content = '<div class="' .
                    'cdx-message ' .
                    'cdx-message--block ' .
                    'cdx-message--error' .
                    '" role="alert">' .
                    '<span class="cdx-message__icon">' .
                    '</span>' .
                    '<div class="cdx-message__content">' .
                    '<p>登入失敗。</p>' .
                    '</div>' .
                    '</div>';
            }
        } elseif ($this->auth_manager->isLoggedIn()) {
            $this->display_content = '<div class="' .
                'cdx-message ' .
                'cdx-message--block ' .
                'cdx-message--warning' .
                '">' .
                '<span class="cdx-message__icon">' .
                '</span>' .
                '<div class="cdx-message__content">' .
                '<p><strong>已登入使用者「' .
                $this->auth_manager->getUsername() .
                '」。</strong></p>' .
                '<p>請使用下方表單改登入另一位使用者。</p>' .
                '</div>' .
                '</div>';
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
            '<div id="user-login-form" class="cdx-form-wrapper cdx-form-wrapper--width-limited">' .
            $this->display_content .
            '<form class="cdx-form" method="post" action="' .
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
            // '<span class="cdx-label__description">' .
            // '</span>' .
            '</div>' .
            '<div class="cdx-field__control">' .
            '<div class="cdx-text-input">' .
            '<input ' .
            'id="user" ' .
            'name="user" ' .
            'size="20" ' .
            'placeholder="請輸入學號/人員代號" ' .
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
            '身分證號/居留證號' .
            '</span>' .
            '</label>' .
            // '<span class="cdx-label__description">' .
            // '</span>' .
            '</div>' .
            '<div class="cdx-field__control">' .
            '<div class="cdx-text-input">' .
            '<input ' .
            'id="password" ' .
            'name="password" ' .
            'size="20" ' .
            'placeholder="請輸入身分證號/居留證號/出生日期（例：19850302）" ' .
            'class="cdx-text-input__input" ' .
            'tabindex="2" ' .
            'required="" ' .
            'type="password"' .
            '>' .
            '</div>' .
            '</div>' .
            '<div class="cdx-field__help-text">' .
            '<p>此欄位僅用於傳至學校OA系統驗證身分，不會儲存至本系統資料庫。<p>' .
            '</div>' .
            '</div>' .
            '<div class="cdx-field">' .
            '<div class="cdx-field__control">' .
            '<button id="user-login-button" ' .
            'class="' .
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
