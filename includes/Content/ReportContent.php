<?php

namespace Isais\Content;

use Isais\Config\MainConfigNames;
use Isais\Content\Content;

class ReportContent extends Content {
    public function __construct($auth_manager, $config, $connection_provider, $context, $title)
    {
        parent::__construct($auth_manager, $config, $connection_provider, $context, $title);

        $item = $this->title->getItem();

        if ($item === 'mid-term') {
            $this->display_title = '期中分組報告';
            $this->display_content = '<p><a href="' .
                $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                $this->context->getUrlHost() .
                '/' .
                $this->config->getOption(MainConfigNames::CONFIG_BASE_DIR) .
                '/tkuisais/docs/資料庫概論期中書面報告_06.pdf' .
                '">PDF檔</a></p>' .
                '<object type="application/pdf" data="' .
                $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                $this->context->getUrlHost() .
                '/' .
                $this->config->getOption(MainConfigNames::CONFIG_BASE_DIR) .
                '/tkuisais/docs/資料庫概論期中書面報告_06.pdf' .
                '"></object>';
        } elseif ($item === 'final_term') {
            $this->display_title = '期末分組報告';
            $this->display_content = '<p><a href="' .
                $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                $this->context->getUrlHost() .
                '/' .
                $this->config->getOption(MainConfigNames::CONFIG_BASE_DIR) .
                '/tkuisais/docs/113-1_0051_資料庫概論_第04組_05.docx.pdf' .
                '">PDF檔</a></p>' .
                '<object type="application/pdf" data="' .
                $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                $this->context->getUrlHost() .
                '/' .
                $this->config->getOption(MainConfigNames::CONFIG_BASE_DIR) .
                '/tkuisais/docs/113-1_0051_資料庫概論_第04組_05.docx.pdf' .
                '"></object>';
        } elseif ($item === 'final_term_2') {
            $this->display_title = '期末成果2.0：系統再最佳化';
            $this->display_content = '<p><a href="' .
                $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                $this->context->getUrlHost() .
                '/' .
                $this->config->getOption(MainConfigNames::CONFIG_BASE_DIR) .
                '/tkuisais/docs/資料庫概論期末書面報告_07.pdf' .
                '">PDF檔</a></p>' .
                '<object type="application/pdf" data="' .
                $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                $this->context->getUrlHost() .
                '/' .
                $this->config->getOption(MainConfigNames::CONFIG_BASE_DIR) .
                '/tkuisais/docs/資料庫概論期末書面報告_07.pdf' .
                '"></object>';
        } else {
            $this->has_content = false;
        }
    }
}
