<?php

namespace Isais\Content;

use Isais\Config\MainConfigNames;
use Isais\Content\Content;
use Isais\Title\Title;

class WikiPageContent extends Content {
    private $created_timestamp;

    public function __construct($auth_manager, $config, $connection_provider, $context, $title)
    {
        parent::__construct($auth_manager, $config, $connection_provider, $context, $title);

        $creatable_unit = $this->title->getCreatableUnit();
        $creatable_unit_creatable_item = $this->title->getCreatableUnitCreatableItem();
        $creatable_club = $this->title->getCreatableClub();
        $creatable_club_creatable_item = $this->title->getCreatableClubCreatableItem();
        $unit_id = $this->title->getUnitId();
        $unit_id_query = 'IS NULL';
        $unit_name = $this->title->getUnitName();
        $page_title = $this->title->getItem();
        $database = $this->connection_provider
            ->getDatabase();

        if ($unit_id !== '') {
            $unit_id_query = '= \'' . $unit_id . '\'';
        }

        $result = $database
            ->newQuery(
                'SELECT ' .
                'wp_created_timestamp, ' .
                'wp_unit_id, ' .
                'wp_title, ' .
                'wp_display_title_label_id, ' .
                'lt_display_title.lt_label_id, ' .
                'lt_display_title.lt_lang_id, ' .
                'lt_display_title.lt_text, ' .
                'wp_description_label_id, ' .
                'lt_description.lt_label_id, ' .
                'lt_description.lt_lang_id, ' .
                'lt_description.lt_text, ' .
                'wp_content_label_id, ' .
                'lt_content.lt_label_id, ' .
                'lt_content.lt_lang_id, ' .
                'lt_content.lt_text ' .
                'FROM wiki_page ' .
                'INNER JOIN lang_text AS lt_display_title ' .
                'ON lt_display_title.lt_label_id = wp_display_title_label_id ' .
                'AND lt_display_title.lt_lang_id = 672 ' .
                'INNER JOIN lang_text AS lt_description ' .
                'ON lt_description.lt_label_id = wp_description_label_id ' .
                'AND lt_display_title.lt_lang_id = 672 ' .
                'INNER JOIN lang_text AS lt_content ' .
                'ON lt_content.lt_label_id = wp_content_label_id ' .
                'AND lt_display_title.lt_lang_id = 672 ' .
                'WHERE wp_unit_id ' . $unit_id_query . ' ' .
                'AND wp_title = \'' . $page_title . '\''
            )
            ->fetchResultSet();

        if ($result->hasRows()) {
            $row = $result->fetchRow();
            $this->created_timestamp = $row[0];
            $this->head_title = $row[6] . ' | ' . $unit_name;
            $this->display_title = $row[6];
            $this->display_subtitle = $row[10];
            $this->display_content = $row[14];
        } else {
            $this->has_content = false;
            $this->head_title = $page_title;
            $this->display_title = $page_title;
            $this->display_content = '<div class="' .
                'cdx-message ' .
                'cdx-message--block ' .
                'cdx-message--error' .
                '" role="alert">' .
                '<span class="cdx-message__icon">' .
                '</span>' .
                '<div class="cdx-message__content">' .
                '<p>您輸入的網址暫無有效頁面。</p>';

            if ($creatable_unit !== '') {
                $this->display_content .= '<p><a href="' .
                    $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                    $this->context->getUrlHost() .
                    '/' .
                    $this->config->getOption(MainConfigNames::CONFIG_BASE_DIR) .
                        '">以單位代碼「' . $creatable_unit . '」建立單位</a></p>';

                if (
                    $creatable_unit_creatable_item !== '' &&
                    !Title::hasUnitModulePath($creatable_unit_creatable_item)
                ) {
                    $this->display_content .= '<p><a href="' .
                        $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                        $this->context->getUrlHost() .
                        '/' .
                        $this->config->getOption(MainConfigNames::CONFIG_BASE_DIR) .
                        '">以單位代碼「' . $creatable_unit . '」建立單位' .
                        '並建立頁面「' . $creatable_unit_creatable_item . '」</a></p>';
                }
            }

            if ($creatable_club !== '') {
                $this->display_content .= '<p><a href="' .
                    $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                    $this->context->getUrlHost() .
                    '/' .
                    $this->config->getOption(MainConfigNames::CONFIG_BASE_DIR) .
                        '">以社團編號「' . $creatable_club . '」建立社團</a></p>';

                if (
                    $creatable_club_creatable_item !== '' &&
                    !Title::hasUnitModulePath($creatable_club_creatable_item)
                ) {
                    $this->display_content .= '<p><a href="' .
                        $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                        $this->context->getUrlHost() .
                        '/' .
                        $this->config->getOption(MainConfigNames::CONFIG_BASE_DIR) .
                        '">以社團編號「' . $creatable_club . '」建立社團' .
                        '並建立頁面「' . $creatable_club_creatable_item . '」</a></p>';
                }
            }

            if (
                !Title::hasUnitModulePath($creatable_unit_creatable_item) &&
                !Title::hasUnitModulePath($creatable_club_creatable_item)
            ) {
                $this->display_content .= '<p><a href="' .
                    $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                    $this->context->getUrlHost() .
                    '/' .
                    $this->config->getOption(MainConfigNames::CONFIG_BASE_DIR) .
                    '">建立頁面「' . $page_title . '」</a></p>';
            }

            $this->display_content .= '<p><a href="' .
                $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                $this->context->getUrlHost() .
                '/' .
                $this->config->getOption(MainConfigNames::CONFIG_BASE_DIR) .
                '">返回首頁</a></p>' .
                '</div>' .
                '</div>';
        }
    }

    public static function hasWikiPage(
        $unit_code,
        $page_title,
        $connection_provider
    ) {
        $unit_id = 'IS NULL';
        $database = $connection_provider->getDatabase();

        if ($unit_code !== '') {
            $result = $database
                ->newQuery(
                    'SELECT unit_id, unit_code ' .
                    'FROM unit ' .
                    'WHERE unit_code = \'' . $unit_code . '\''
                )
                ->fetchResultSet();

            if ($result->hasRows()) {
                $unit_id = $result->fetchRow();
                $unit_id = $unit_id[0];
                $unit_id = '= \'' . $unit_id . '\'';
            } else {
                return false;
            }
        }

        return $database
            ->newQuery(
                'SELECT wp_unit_id, wp_title ' .
                'FROM wiki_page ' .
                'WHERE wp_unit_id ' . $unit_id . ' ' .
                'AND wp_title = \'' . $page_title . '\''
            )
            ->fetchResultSet()
            ->hasRows();
    }

    public function showNotFoundPage()
    {
        return false;
    }
}
