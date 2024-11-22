<?php

namespace Isais\Content;

use Isais\Config\MainConfigNames;
use Isais\Content\Content;

class MainPageContent extends Content {
    public function __construct($auth_manager, $config, $connection_provider, $context, $title)
    {
        parent::__construct($auth_manager, $config, $connection_provider, $context, $title);

        $this->display_title = '歡迎使用iSAIS校務資訊系統！';
        $this->display_content = '<h2>最新公告</h2>' .
            // '<h2>近期活動</h2>' .
            // '<h2>課程</h2>' .
            '<h2>單位</h2>' .
            '<h2>社團</h2>' .
            // '<h2>近期活動</h2>' .
            // '<h2>銀行清單</h2>' .
            '<h2>課程報告</h2>' .
            '<ul>' .
            '<li>' .
            '<a href="' .
            $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
            $this->context->getUrlHost() .
            '/' .
            str_replace(
                '$1',
                'report/mid-term/',
                $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
            ) .
            '">' .
            '期中報告' .
            '</a>' .
            '</li>' .
            '<li>' .
            '<a href="' .
            $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
            $this->context->getUrlHost() .
            '/' .
            str_replace(
                '$1',
                'report/final_term/',
                $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
            ) .
            '">' .
            '期末報告' .
            '</a>' .
            '</li>' .
            '</ul>' .
            '<a href="' .
            $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
            $this->context->getUrlHost() .
            '/' .
            str_replace(
                '$1',
                'report/',
                $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
            ) .
            '">' .
            '查閱所有課程報告 →' .
            '</a>';
    }
}
