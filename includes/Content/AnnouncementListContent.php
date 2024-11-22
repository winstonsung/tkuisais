<?php

namespace Isais\Content;

use Isais\Config\MainConfigNames;
use Isais\Content\ListContent;

class AnnouncementListContent extends ListContent {
    public function __construct($auth_manager, $config, $connection_provider, $context, $title)
    {
        parent::__construct($auth_manager, $config, $connection_provider, $context, $title);

        if ($this->title->getUnit() !== '') {
            $this->display_title = $this->title->getUnitName() . '公告';
        } else {
            $this->display_title = '公告';
        }

        $search_type = 'text_all';

        if (
            isset($_GET['type']) &&
            in_array($_GET['type'], array('text_subject', 'text_content', 'announcement_id'))
        ) {
            $search_type = $_GET['type'];
        }

        $content = '<div class="' .
            'cdx-form-wrapper ' .
            'cdx-form-wrapper-padded ' .
            'cdx-form-wrapper-framed' .
            '">' .
            '<form method="get" action=".">' .
            '<div class="cdx-field">' .
            '<div class="cdx-label">' .
            '<label class="cdx-label__label" for="type">' .
            '<span class="cdx-label__label__text">' .
            '搜尋類型' .
            '</span>' .
            '</label>' .
            '</div>' .
            '<div class="cdx-field__control">' .
            '<div class="cdx-radio">' .
            '<div class="cdx-radio__wrapper">' .
            '<input class="cdx-radio__input" type="radio" name="type" value="text_all"' .
            (
                $search_type === 'text_all' ?
                ' checked' :
                ''
            ) .
            '>' .
            '<span class="cdx-radio__icon">' .
            '</span>' .
            '<div class="cdx-radio__label cdx-label">' .
            '<label for="type" class="cdx-label__label">' .
            '<span class="cdx-label__label__text">' .
            '搜尋全文' .
            '</span>' .
            '</label>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '<div class="cdx-radio">' .
            '<div class="cdx-radio__wrapper">' .
            '<input class="cdx-radio__input" type="radio" name="type" value="text_subject"' .
            (
                $search_type === 'text_subject' ?
                ' checked' :
                ''
            ) .
            '>' .
            '<span class="cdx-radio__icon">' .
            '</span>' .
            '<div class="cdx-radio__label cdx-label">' .
            '<label for="type" class="cdx-label__label">' .
            '<span class="cdx-label__label__text">' .
            '搜尋標題' .
            '</span>' .
            '</label>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '<div class="cdx-radio">' .
            '<div class="cdx-radio__wrapper">' .
            '<input class="cdx-radio__input" type="radio" name="type" value="text_content"' .
            (
                $search_type === 'text_content' ?
                ' checked' :
                ''
            ) .
            '>' .
            '<span class="cdx-radio__icon">' .
            '</span>' .
            '<div class="cdx-radio__label cdx-label">' .
            '<label for="type" class="cdx-label__label">' .
            '<span class="cdx-label__label__text">' .
            '搜尋內文' .
            '</span>' .
            '</label>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '<div class="cdx-radio">' .
            '<div class="cdx-radio__wrapper">' .
            '<input class="cdx-radio__input" type="radio" name="type" value="announcement_id"' .
            (
                $search_type === 'announcement_id' ?
                ' checked' :
                ''
            ) .
            '>' .
            '<span class="cdx-radio__icon">' .
            '</span>' .
            '<div class="cdx-radio__label cdx-label">' .
            '<label for="type" class="cdx-label__label">' .
            '<span class="cdx-label__label__text">' .
            '搜尋公告編號' .
            '</span>' .
            '</label>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '<div class="cdx-field">' .
            '<div class="cdx-label">' .
            '<label class="cdx-label__label" for="q">' .
            '<span class="cdx-label__label__text">' .
            '搜尋文字' .
            '</span>' .
            '</label>' .
            '</div>' .
            '<div class="cdx-field__control">' .
            '<div class="cdx-search-input">' .
            '<div class="cdx-search-input__input-wrapper">' .
            '<div class="cdx-text-input cdx-text-input--has-start-icon">' .
            '<input class="cdx-text-input__input" type="search" name="q" placeholder="搜尋銀行"' .
            (
                isset( $_GET['q'] ) ?
                ' value="' . trim( $_GET['q'] ) . '"' :
                ''
            ) .
            '>' .
            '<span class="cdx-text-input__icon cdx-text-input__start-icon">' .
            '</span>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '<div class="cdx-field">' .
            '<button type="submit" class="' .
            'cdx-button ' .
            'cdx-button--action-progressive ' .
            'cdx-button--weight-primary' .
            '">' .
            '搜尋' .
            '</div>' .
            '</button>' .
            '</form>' .
            '</div>';

        $sql = <<<SQL
SELECT
announcement_id,
announcement_created_timestamp,
announcement_status,
announcement_expire_timestamp,
announcement_ui_id,
announcement_subject_label_id,
lt_announcement_subject.lt_label_id,
lt_announcement_subject.lt_lang_id,
lt_announcement_subject.lt_text,
announcement_content_label_id,
lt_announcement_content.lt_label_id,
lt_announcement_content.lt_lang_id,
lt_announcement_content.lt_text
FROM announcement
INNER JOIN lang_text AS lt_announcement_subject
ON lt_announcement_subject.lt_label_id = announcement_subject_label_id
AND lt_announcement_subject.lt_lang_id = 672
INNER JOIN lang_text AS lt_announcement_content
ON lt_announcement_content.lt_label_id = announcement_content_label_id
AND lt_announcement_content.lt_lang_id = 672
WHERE
SQL;

        if (isset($_GET['q']) && trim($_GET['q']) !== '') {
            if ($search_type === 'text_all') {
                $sql .= ' lt_announcement_subject.lt_text LIKE \'%' .
                    str_replace( '\'', '\\\'', trim($_GET['q'])) .
                    '%\';';
            } elseif ($search_type === 'text_subject') {
                $sql .= ' lt_announcement_subject.lt_text LIKE \'%' .
                    str_replace( '\'', '\\\'', trim($_GET['q'])) .
                    '%\' OR lt_announcement_subject.lt_text LIKE \'%' .
                    str_replace( '\'', '\\\'', trim($_GET['q'])) .
                    '%\';';
            } elseif ($search_type === 'text_content') {
                $sql .= ' lt_announcement_content.lt_text LIKE \'%' .
                    str_replace( '\'', '\\\'', trim($_GET['q'])) .
                    '%\';';
            } elseif ($search_type === 'announcement_id') {
                $sql .= ' announcement_id = \'' .
                    str_replace('\'', '\\\'', trim($_GET['q'])) .
                    '%\';';
            } else {
                $content .= '<div class="cdx-message cdx-message--block cdx-message--warning">' .
                    '<span class="cdx-message__icon">' .
                    '</span>' .
                    '<div class="cdx-message__content">' .
                    '<p><strong>參數錯誤。</strong></p>' .
                    '<p>請修正搜尋條件。</p>' .
                    '</div>' .
                    '</div>' .
                    $content;

                $this->display_content = $content;

                return;
            }
        }

        $result = $this->connection_provider
            ->getDatabase()
            ->newQuery($sql);

        if (!$result->hasRows()) {
            $content .= '<div class="cdx-message cdx-message--block cdx-message--warning">' .
                '<span class="cdx-message__icon">' .
                '</span>' .
                '<div class="cdx-message__content">' .
                '<p><strong>查無資料。</strong></p>' .
                '<p>請嘗試其他搜尋條件。</p>' .
                '</div>' .
                '</div>';
        } else {
            $row = $result->fetchRow();
            $content .= '<div class="cdx-card-group">';

            while ($row !== null) {
                $content .= '<a href="' .
                    $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                    $this->context->getUrlHost() .
                    '/' .
                    str_replace(
                        '$1',
                        'bank/' . $row[1] . '/',
                        $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
                    ) .
                    '" class="cdx-card cdx-card--is-link">' .
                    '<span class="cdx-thumbnail cdx-card__thumbnail">' .
                    '<span style="' .
                    'background-image: ' .
                    'url(&quot;' .
                    $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                    $this->context->getUrlHost() .
                    '/' .
                    $this->config->getOption(MainConfigNames::CONFIG_SCRIPT_PATH) .
                    'resources/assets/Bank_' . $row[0] . '.jpg' .
                    '&quot;);' .
                    '" class="cdx-thumbnail__image">' .
                    '</span>' .
                    '</span>' .
                    '<span class="cdx-card__text">' .
                    '<span class="cdx-card__text__title">' .
                    htmlspecialchars($row[5]) .
                    '</span>' .
                    '<span class="cdx-card__text__description">' .
                    '<!-- Description -->' .
                    '</span>' .
                    '<span class="cdx-card__text__supporting-text">' .
                    '銀行代碼：' . htmlspecialchars($row[1]) .
                    '</span>' .
                    '</span>' .
                    '</a>';

                $row = $result->fetchRow();
            }

            $content .= '</div>';
        }

        $this->display_content = $content;
    }

    public function getListItems()
    {
    }
}
