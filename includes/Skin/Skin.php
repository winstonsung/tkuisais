<?php

namespace Isais\Skin;

use Isais\Config\MainConfigNames;

class Skin {
    private $config;

    private $context;

    private $resource_loader;

    private $title;

    public function __construct(
        $config,
        $context,
        $resource_loader,
        $title
    ) {
        $this->config = $config;
        $this->context = $context;
        $this->resource_loader = $resource_loader;
        $this->title = $title;
    }

    public function getSiteName() {
        $site_name = $this->config
            ->getOption(MainConfigNames::CONFIG_SITE_CANONICAL_NAME);
        return $site_name;
    }

    public function getHeadTitleText() {
        return $this->getSiteName();
    }

    public function getHeadTitle() {
        return '<title>' .
            $this->getHeadTitleText() .
            '</title>';
    }

    public function getHeadMetaTags() {
        return '<meta charset="UTF-8" />' .
            '<meta name="viewport" content="' .
            'width=device-width, ' .
            'initial-scale=1.0, ' .
            'user-scalable=yes, ' .
            'minimum-scale=0.25, ' .
            'maximum-scale=5.0' .
            '">' .
            '<meta name="generator" content="iSAIS 0.1">' .
            '<meta name="format-detection" content="telephone=no">' .
            '<meta property="og:type" content="website">' .
            '<meta property="og:site_name" content="' .
            $this->getSiteName() .
            '">' .
            '<meta property="og:title" content="' .
            $this->getHeadTitleText() .
            '">';
    }

    public function getHeadLinkTags() {
        return '<link rel="apple-touch-icon" href="' .
            $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
            $this->context->getUrlHost() .
            '/' .
            $this->config->getOption(MainConfigNames::CONFIG_SITE_APPLE_TOUCH_ICON) .
            '">' .
            '<link rel="icon" href="' .
            $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
            $this->context->getUrlHost() .
            '/' .
            $this->config->getOption(MainConfigNames::CONFIG_SITE_FAVICON) .
            '">' .
            '<link rel="canonical" href="' .
            $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
            $this->context->getUrlHost() .
            '/' .
            str_replace(
                '$1',
                $this->context->getTitleFullText(),
                $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
            ) .
            '">';
    }

    public function getHeadStylesheets() {
        return '<link rel="stylesheet" href="' .
            $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
            $this->context->getUrlHost() .
            '/' .
            $this->config->getOption(MainConfigNames::CONFIG_LOAD_SCRIPT) .
            '?type=styles&modules=' .
            $this->resource_loader->getModulesParameter('styles') .
            '" />';
    }

    public function getHeadScripts() {
        return '<script src="' .
            $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
            $this->context->getUrlHost() .
            '/' .
            $this->config->getOption(MainConfigNames::CONFIG_LOAD_SCRIPT) .
            '?type=scripts&modules=' .
            $this->resource_loader->getModulesParameter('scripts') .
            '" async></script>';
    }

    public function getHead() {
        return '<head>' .
            $this->getHeadTitle() .
            $this->getHeadMetaTags() .
            $this->getHeadLinkTags() .
            $this->getHeadStylesheets() .
            $this->getHeadScripts() .
            '</head>';
    }

    public function getBodyJumpLinks() {
        return '<a class="site-jump-link" href="#content">跳至內容</a>';
    }

    public function getBodyHeader() {
        return '<header class="site-header">' .
            '<div class="site-name">' .
            '<a class="site-name-link" href=".">' .
            '<span class="site-name-text">' .
            $this->getSiteName() .
            '</span>' .
            '</a>' .
            '</div>' .
            '</header>';
    }

    public function getBodyNav() {
        return '<nav id="nav" class="site-nav">' .
            '<ul>' .
            '<li>' .
            '<span class="site-nav-heading">導覽</span>' .
            '<ul>' .
            '<li>' .
            '<a href=".">首頁</a>' .
            '</li>' .
            '</ul>' .
            '<li>' .
            '<span class="site-nav-heading">工具</span>' .
            '<ul>' .
            '<li>' .
            '<a href="https://github.com/winstonsung/tkuisais">GitHub原始碼庫</a>' .
            '</li>' .
            '<li>' .
            '<a href="' .
            $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
            $this->context->getUrlHost() .
            '/' .
            $this->config->getOption(MainConfigNames::CONFIG_BASE_DIR) .
            'tkufd/docs/資料庫概論期中書面報告_06.pdf">期中報告</a>' .
            '</li>' .
            '<li>' .
            '<a href="http://163.13.175.8/php-pwiki/">資料庫概論 - Pwiki</a>' .
            '</li>' .
            '<li>' .
            '<a href="http://163.13.175.8/phpMyAdmin/">phpMyAdmin</a>' .
            '</li>' .
            '<li>' .
            '<a href="phpinfo.php">伺服器資訊</a>' .
            '</li>' .
            '</ul>' .
            '</li>' .
            '</ul>' .
            '</nav>';
    }

    public function displayBodyPageTitle() {
        return !$this->title->isMainPage();
    }

    public function getBodyPageTitle() {
        if ($this->title->isMainPage()) {
            return $this->getSiteName();
        }

        return $this->getSiteName();
    }

    public function getBodyPageContent() {
        return '<p>Unit: ' . $this->title->getUnit() . '</p>' .
            '<p>Module: ' . $this->title->getModule() . '</p>' .
            '<p>Item: ' . $this->title->getItem() . '</p>' .
            $this->title->getContent();
    }

    public function getBodyMain() {
        $content = '<main id="#content" class="site-main">';

        if ( $this->displayBodyPageTitle() === false ) {
            $content .= '<h1 class="page-title hidden">';
        } else {
            $content .= '<h1 class="page-title">';
        }

        $content .= $this->getBodyPageTitle();
        $content .= '</h1>';
        $content .= '<div class="page-content">';
        $content .= $this->getBodyPageContent();
        $content .= '</div>';
        $content .= '</main>';
        return $content;
    }

    public function getBodyFooter() {
        return '<footer class="site-footer">' .
            '</footer>';
    }

    public function getBodyContainer() {
        return '<div class="site-body-container">' .
            $this->getBodyHeader() .
            $this->getBodyNav() .
            $this->getBodyMain() .
            $this->getBodyFooter() .
            '</div>';
    }

    public function getBody() {
        return $this->getBodyJumpLinks() .
            $this->getBodyContainer();
    }

    public function getHtml() {
        return '<!DOCTYPE html>' .
            '<html lang="zh-Hant-TW" dir="ltr">' .
            $this->getHead() .
            $this->getBody() .
            '</html>';
    }
}
