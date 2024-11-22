<?php

namespace Isais\Content;

use Isais\Content\Content;

class UserLogoutContent extends Content
{
    public function __construct($auth_manager, $config, $connection_provider, $context, $title)
    {
        parent::__construct($auth_manager, $config, $connection_provider, $context, $title);

        $this->display_title = '登出';

        if (
            isset($params_post['user']) &&
            isset($params_post['logout'])
        ) {
        } elseif ($this->auth_manager->isLoggedIn()) {
            $this->display_content = '<p>您是否確定要登出使用者「' . '」？</p>';
        }

        $this->display_content =  '<div class="' .
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
