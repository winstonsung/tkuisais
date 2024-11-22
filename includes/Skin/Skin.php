<?php

namespace Isais\Skin;

use Isais\Config\MainConfigNames;

class Skin
{
    private $auth_manager;

    private $config;

    private $content;

    private $context;

    private $resource_loader;

    private $title;

    public function __construct(
        $auth_manager,
        $config,
        $context,
        $resource_loader,
        $title
    ) {
        $this->auth_manager = $auth_manager;
        $this->config = $config;
        $this->content = $title->getContent();
        $this->context = $context;
        $this->resource_loader = $resource_loader;
        $this->title = $title;
    }

    public function getSiteName()
    {
        $site_name = $this->config
            ->getOption(MainConfigNames::CONFIG_SITE_CANONICAL_NAME);
        return $site_name;
    }

    public function getHeadTitleText() {
        if ($this->title->isMainPage()) {
            return $this->getSiteName();
        }

        return $this->content->getHeadTitle() . ' | ' . $this->getSiteName();
    }

    public function getHeadTitle()
    {
        return '<title>' .
            $this->getHeadTitleText() .
            '</title>';
    }

    public function getHeadMetaTags()
    {
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

    public function getHeadLinkTags()
    {
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

    public function getHeadStylesheets()
    {
        return '<link rel="stylesheet" href="' .
            $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
            $this->context->getUrlHost() .
            '/' .
            $this->config->getOption(MainConfigNames::CONFIG_LOAD_SCRIPT) .
            '?type=styles&modules=' .
            $this->resource_loader->getModulesParameter('styles') .
            '" />';
    }

    public function getHeadScripts()
    {
        return '<script src="' .
            $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
            $this->context->getUrlHost() .
            '/' .
            $this->config->getOption(MainConfigNames::CONFIG_LOAD_SCRIPT) .
            '?type=scripts&modules=' .
            $this->resource_loader->getModulesParameter('scripts') .
            '" async></script>';
    }

    public function getHead()
    {
        return '<head>' .
            $this->getHeadTitle() .
            $this->getHeadMetaTags() .
            $this->getHeadLinkTags() .
            $this->getHeadStylesheets() .
            $this->getHeadScripts() .
            '</head>';
    }

    public function getBodyJumpLinks()
    {
        return '<a class="site-jump-link" href="#content">跳至內容</a>';
    }

    public function getBodyHeaderSiteName()
    {
        return '<div class="site-name">' .
            '<a class="site-name-link" href="' .
            $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
            $this->context->getUrlHost() .
            '/' .
            str_replace(
                '$1',
                '',
                $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
            ) .
            '">' .
            '<span class="site-name-text">' .
            $this->getSiteName() .
            '</span>' .
            '</a>' .
            '</div>';
    }

    public function getBodyHeaderSiteMenuUserLink()
    {
        $content = '<div class="site-menu site-user-page-link">' .
            '<span class="site-menu-label">';

        if ($this->auth_manager->isLoggedIn()) {
            $content .= '<a href="' .
                $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                $this->context->getUrlHost() .
                '/' .
                str_replace(
                    '$1',
                    'user/' . $this->auth_manager->getUserId(),
                    $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
                ) .
                '">' .
                '<span>' .
                $this->auth_manager->getUsername() .
                '</span>' .
                '</a>';
        } else {
            $content .= '<a href="' .
                $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                $this->context->getUrlHost() .
                '/' .
                str_replace(
                    '$1',
                    'login/' . (
                        ($this->context->getTitleFullText() !== '') ?
                        '?returnto=' . $this->context->getTitleFullText() :
                        ''
                    ),
                    $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
                ) .
                '" title="登入 [Alt+Shift+o]" accesskey="o">' .
                '登入' .
                '</a>';
        }

        $content .= '</span>' .
            '</div>';

        return $content;
    }

    public function getBodyHeaderSiteMenuUserMenu()
    {
        $content = '<div class="site-menu ' .
            'site-user-menu ' .
            'dropdown ' .
            'dropdown-button-flush-right';

        if (!$this->auth_manager->isLoggedIn()) {
            $content .= ' hidden';
        }

        $content .= '">' .
            '<input type="checkbox" id="user-links-dropdown-checkbox" ' .
            'role="button" aria-haspopup="true" class="dropdown-checkbox" ' .
            'aria-label="個人工具">' .
            '<label id="user-links-dropdown-label" ' .
            'for="user-links-dropdown-checkbox" ' .
            'class="site-menu-label dropdown-label ' .
            'cdx-button ' .
            'cdx-button--fake-button ' .
            'cdx-button--fake-button--enabled ' .
            'cdx-button--weight-quiet ' .
            'cdx-button--icon-only' .
            '" aria-hidden="true">' .
            '<svg class="cdx-icon cdx-icon--medium" xmlns="http://www.w3.org/2000/svg" ' .
            'width="20" height="20" viewBox="0 0 20 20">' .
            '<g fill="#ffffff">' .
            '<path d="M10 11c-5.92 0-8 3-8 5v3h16v-3c0-2-2.08-5-8-5" />' .
            '<circle cx="10" cy="5.5" r="4.5" />' .
            '</g>' .
            '</svg> ' .
            '<span class="dropdown-label-text">個人工具</span>' .
            '<svg class="cdx-icon cdx-icon--x-small" xmlns="http://www.w3.org/2000/svg" ' .
            'xmlns:xlink="http://www.w3.org/1999/xlink" ' .
            'width="20" height="20" viewBox="0 0 20 20">' .
            '<g fill="#ffffff">' .
            '<path d="m17.5 4.75-7.5 7.5-7.5-7.5L1 6.25l9 9 9-9z" />' .
            '</g>' .
            '</svg>' .
            '</label>' .
            '<div class="dropdown-content">' .
            '<div id="p-personal" class="dropdown-menu site-portlet" title="使用者選單">' .
            '<div class="dropdown-menu-content">' .
            '<ul class="dropdown-content-list">';

        if ($this->auth_manager->isLoggedIn()) {
            $content .= '<li id="pt-logout" class="dropdown-content-list-item">' .
                '<a href="' .
                $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                $this->context->getUrlHost() .
                '/' .
                str_replace(
                    '$1',
                    'logout/' . (
                        ($this->context->getTitleFullText() !== '') ?
                        '?returnto=' . $this->context->getTitleFullText() :
                        ''
                    ),
                    $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
                ) .
                '" title="登出 [Alt+Shift+o]" accesskey="o">' .
                '<svg class="cdx-icon cdx-icon--medium" xmlns="http://www.w3.org/2000/svg" ' .
                'width="20" height="20" viewBox="0 0 20 20">' .
                '<g fill="#000000">' .
                '<path d="M3 3h8V1H3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8v-2H3z" />' .
                '<path d="M13 5v4H5v2h8v4l6-5z" />' .
                '</g>' .
                '</svg> ' .
                '<span>登出</span>' .
                '</a>' .
                '</li>';
        }

        $content .= '</ul>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '</div>';

        return $content;
    }

    public function getBodyHeaderSiteMenu()
    {
        return '<div class="site-header-menu">' .
            $this->getBodyHeaderSiteMenuUserLink() .
            $this->getBodyHeaderSiteMenuUserMenu() .
            '</div>';
    }

    public function getBodyHeader()
    {
        return '<header class="site-header">' .
            $this->getBodyHeaderSiteName() .
            $this->getBodyHeaderSiteMenu() .
            '</header>';
    }

    public function getBodyNav()
    {
        return '<nav id="nav" class="site-nav">' .
            '<ul>' .
            '<li>' .
            '<span class="site-nav-heading">導覽</span>' .
            '<ul>' .
            '<li>' .
            '<a href="' .
            $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
            $this->context->getUrlHost() .
            '/' .
            str_replace(
                '$1',
                '',
                $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
            ) .
            '">首頁</a>' .
            '</li>' .
            '<li>' .
            '<a href="' .
            $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
            $this->context->getUrlHost() .
            '/' .
            str_replace(
                '$1',
                'announcement/',
                $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
            ) .
            '">公告</a>' .
            '</li>' .
            '<li>' .
            '<a href="' .
            $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
            $this->context->getUrlHost() .
            '/' .
            str_replace(
                '$1',
                'report/',
                $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
            ) .
            '">課程報告</a>' .
            '</li>' .
            '<li>' .
            '<a href="' .
            $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
            $this->context->getUrlHost() .
            '/' .
            $this->config->getOption(MainConfigNames::CONFIG_API_SCRIPT) .
            '">應用程式介面</a>' .
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
            str_replace(
                '$1',
                'report/',
                $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
            ) .
            '">課程報告</a>' .
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

    public function displayBodyPageTitle()
    {
        return true;
    }

    public function getBodyPageTitle()
    {
        return $this->content->getTitle();
    }

    public function getBodyPageContent()
    {
        // return '<div class="hidden">' .
        //     '<p>Unit: ' . $this->title->getUnit() . '</p>' .
        //     '<p>Module: ' . $this->title->getModule() . '</p>' .
        //     '<p>Item: ' . $this->title->getItem() . '</p>' .
        //     '</div>' .
        return $this->content->getContent();
    }

    public function getBodyMain()
    {
        $content = '<main id="#content" class="site-main">';

        if ( $this->title->getFullText() !== '' ) {
            $content .= '<nav class="page-breadcrumb-nav">' .
                '<ul>' .
                '<li><a>breadcrumb</a></li>' .
                '<li><a>navigation</a></li>' .
                '</ul>' .
                '</nav>';
        }

        if ( $this->displayBodyPageTitle() === false ) {
            $content .= '<h1 class="page-title hidden">';
        } else {
            $content .= '<h1 class="page-title">';
        }

        $content .= $this->getBodyPageTitle() .
            '</h1>' .
            '<div class="page-content">' .
            $this->getBodyPageContent() .
            '</div>' .
            '</main>';

        return $content;
    }

    public function getBodyFooter()
    {
        return '<footer class="site-footer">' .
            '<ul class="site-footer-icons">' .
            '<li class="site-footer-icon-codex-design-system">' .
            '<a href="https://doc.wikimedia.org/codex/v0.18.0/" class="' .
            'cdx-button ' .
            'cdx-button--fake-button ' .
            'cdx-button--size-large ' .
            'cdx-button--fake-button--enabled' .
            '">' .
            '<svg class="logo" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">' .
            '<path d="M6.08 5.555a6.048 6.048 0 0 0 3.055 10.593v-7.54L6.08 5.556zm7.828.004-3.05 3.05v7.536a6.048 6.048 0 0 0 3.05-10.587z"/>' .
            '<path d="M3.414 2.89C1.424 4.69.164 7.287.168 10.173c.007 5.406 4.42 9.806 9.828 9.806 5.407 0 9.82-4.4 9.828-9.806.004-2.886-1.255-5.482-3.246-7.285L14.865 4.6a7.355 7.355 0 0 1 2.524 5.568c-.007 4.09-3.3 7.375-7.394 7.375S2.61 14.26 2.604 10.17a7.355 7.355 0 0 1 2.523-5.568L3.414 2.89z"/>' .
            '<circle cx="10" cy="3.32" r="3.32"/>' .
            '</svg>' .
            '<span>' .
            'Codex' .
            '</span>' .
            '</a>' .
            '</li>' .
            '<li class="site-footer-icon-copyright">' .
            '<a href="https://creativecommons.org/publicdomain/zero/1.0/deed.en" class="' .
            'cdx-button ' .
            'cdx-button--fake-button ' .
            'cdx-button--size-large ' .
            'cdx-button--fake-button--enabled' .
            '">' .
            '<img width="88" height="31" loading="lazy" src="' .
            $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
            $this->context->getUrlHost() .
            '/' .
            $this->config->getOption(MainConfigNames::CONFIG_SCRIPT_PATH) .
            'resources/assets/License_CC-0.png' .
            '" alt="Creative Commons Zero v1.0 Universal">' .
            '</a>' .
            '</li>' .
            '</ul>' .
            '</footer>';
    }

    public function getBodyContainer()
    {
        return '<div class="site-body-container">' .
            $this->getBodyHeader() .
            $this->getBodyNav() .
            $this->getBodyMain() .
            $this->getBodyFooter() .
            '</div>';
    }

    public function getBody()
    {
        return $this->getBodyJumpLinks() .
            $this->getBodyContainer();
    }

    public function getHtml()
    {
        return '<!DOCTYPE html>' .
            '<html lang="zh-Hant-TW" dir="ltr">' .
            $this->getHead() .
            $this->getBody() .
            '</html>';
    }
}
