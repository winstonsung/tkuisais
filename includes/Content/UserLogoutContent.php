<?php

namespace Isais\Content;

use Isais\Config\MainConfigNames;
use Isais\Content\Content;
use Isais\Context\Context;

class UserLogoutContent extends Content
{
    public function __construct($auth_manager, $config, $connection_provider, $context, $title)
    {
        parent::__construct($auth_manager, $config, $connection_provider, $context, $title);

        $params_all = $this->context->getUrlAllParameters();
        $params_post = $this->context->getUrlPostParameters();
        $this->display_title = '登出';

        if (
            isset($params_post['user']) &&
            isset($params_post['logout']) &&
            in_array($params_post['logout'], array('1', 'true'))
        ) {
            $user_id = $params_post['user'];
            $result = false;

            if ($user_id === $this->auth_manager->getUserId()) {
                $result = $this->auth_manager->logout();
            }

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
                $this->display_content = '<p>已登出使用者「' . $username . '」。</p>' .
                    '<p>正在重新導向……</p>';

                return;
            }

            $this->display_content = '<div class="' .
                'cdx-message ' .
                'cdx-message--block ' .
                'cdx-message--error' .
                '" role="alert">' .
                '<span class="cdx-message__icon">' .
                '</span>' .
                '<div class="cdx-message__content">' .
                '<p>登出失敗。請稍後再試</p>' .
                '</div>' .
                '</div>';
        }

        if ($this->auth_manager->isLoggedIn()) {
            $this->display_content .= '<div id="user-logout-form" class="cdx-form-wrapper">' .
                '<form class="cdx-form" method="post" action="' .
                $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                $this->context->getUrlHost() .
                '/' .
                str_replace(
                    '$1',
                    'logout/' . (
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
                '<p>您是否確定要登出使用者「' .
                $this->auth_manager->getUsername() .
                '」？</p>' .
                '<input type="hidden" name="user" value="' .
                $this->auth_manager->getUserId() .
                '" />' .
                '<div class="cdx-field">' .
                '<div class="cdx-field__control">' .
                '<button id="user-logout-button" ' .
                'class="' .
                'cdx-button ' .
                'cdx-button--weight-primary ' .
                'cdx-button--action-progressive ' .
                '" name="logout" value="1" type="submit" ' .
                'tabindex="6"' .
                '>' .
                '登出' .
                '</button>' .
                '<a class="' .
                'cdx-button ' .
                'cdx-button--fake-button ' .
                'cdx-button--fake-button--enabled' .
                '" ' .
                'tabindex="7"' .
                'href="' .
                $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                $this->context->getUrlHost() .
                '/' .
                str_replace(
                    '$1',
                    (
                        (
                            isset($params_all['returnto']) &&
                            $params_all['returnto'] !== ''
                        ) ?
                        $params_all['returnto'] :
                        ''
                    ),
                    $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
                ) .
                '">' .
                '取消' .
                '</a>' .
                '</div>' .
                '</div>' .
                '</form>' .
                '</div>';

            return;
        }

        $this->display_content = '<div class="' .
            'cdx-message ' .
            'cdx-message--block ' .
            'cdx-message--error' .
            '" role="alert">' .
            '<span class="cdx-message__icon">' .
            '</span>' .
            '<div class="cdx-message__content">' .
            '<p>尚未登入，無法登出。</p>' .
            '</div>' .
            '</div>';
    }
}
