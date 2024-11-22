<?php

namespace Isais\Content;

use Isais\Config\MainConfigNames;
use Isais\Content\Content;

class NotFoundContent extends Content {
    public function __construct($auth_manager, $config, $connection_provider, $context, $title)
    {
        parent::__construct($auth_manager, $config, $connection_provider, $context, $title);

        $this->has_content = false;
        $this->display_title = '無效網址';
        $this->display_content = '<div class="' .
            'cdx-message ' .
            'cdx-message--block ' .
            'cdx-message--error' .
            '" role="alert">' .
            '<span class="cdx-message__icon">' .
            '</span>' .
            '<div class="cdx-message__content">' .
            '<p>您輸入的網址不是有效網址。</p>' .
            '<p><a href="' .
            $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
            $this->context->getUrlHost() .
            '/' .
            $this->config->getOption(MainConfigNames::CONFIG_BASE_DIR) .
            '">返回首頁</a></p>' .
            '</div>' .
            '</div>';
    }
}
