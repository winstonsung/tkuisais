<?php

namespace Isais\Content;

use Isais\Config\MainConfigNames;
use Isais\Content\Content;

class UserLoginContent extends Content
{
    public function __construct($auth_manager, $config, $connection_provider, $context, $title)
    {
        parent::__construct($auth_manager, $config, $connection_provider, $context, $title);

        $params_all = $this->context->getUrlAllParameters();
        $params_post = $this->context->getUrlPostParameters();

        if (
            isset($params_post['user']) &&
            isset($params_post['token'])
        ) {
            $user_identity_code = $params_post['user'];
            $token = $params_post['token'];

            if ($this->auth_manager->login($user_identity_code, $token)) {
                $this->display_title = '已登入';
                $this->display_content = '<p>正在重新導向……</p>';

                return;
            } else {
                $this->display_content = '<p>登入失敗。</p>';
            }
        }

        $this->display_title = '登入';
        $this->display_content .= '<p>' .
            '要登入' .
            $this->config
                ->getOption(MainConfigNames::CONFIG_SITE_CANONICAL_NAME) .
            '，' .
            '您必須啟用' .
            '<a href="https://zh.wikipedia.org/zh-tw/Cookie" title="cookie">cookie</a>' .
            '功能。'.
            '</p>' .
            '<div id="user-login-form" class="cdx-form-wrapper">' .
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
            '<label class="cdx-label__label" for="token">' .
            '<span class="cdx-label__label__text">' .
            '授權碼' .
            '</span>' .
            '</label>' .
            '<span class="cdx-label__description">' .
            '此欄位透過單一登入系統自動填寫' .
            '</span>' .
            '</div>' .
            '<div class="cdx-field__control">' .
            '<div class="cdx-text-input">' .
            '<input ' .
            'id="token" ' .
            'name="token" ' .
            'size="20" ' .
            'placeholder="請填寫下方表單取得授權碼" ' .
            'class="cdx-text-input__input" ' .
            'tabindex="2" ' .
            'disabled="" ' .
            'required="" ' .
            'type="password"' .
            '>' .
            '</div>' .
            '</div>' .
            // '<div class="cdx-field__help-text">' .
            // '</div>' .
            '</div>' .
            '<iframe id="sso-iframe" ' .
            'src="https://sso.tku.edu.tw/sis/Authorize/Sso/SsoLogin?&amp;embed=YES" ' .
            'frameborder="0" scrolling="no" ' .
            // 'style="width: 0; height: 0;" ' .
            'style="width: 450px; height: 250px;" ' .
            'tc-textcontent="true" data-tc-id="w-0.6093096948725547"' .
            '>' .
            '</iframe>' .
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
