<?php

namespace Isais\Content;

use Isais\Config\MainConfigNames;
use Isais\Content\ListContent;

class ReportListContent extends ListContent {
    public function __construct($auth_manager, $config, $connection_provider, $context, $title)
    {
        parent::__construct($auth_manager, $config, $connection_provider, $context, $title);

        $this->display_title = '課程報告';
        $this->display_content = '<ul>' .
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
            '</ul>';
    }

    public function getListItems()
    {
    }
}
