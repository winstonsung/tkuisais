<?php

namespace Isais\Content;

use Isais\Config\MainConfigNames;
use Isais\Content\ListContent;

class BankListContent extends ListContent {
    public function __construct($auth_manager, $config, $connection_provider, $context, $title)
    {
        parent::__construct($auth_manager, $config, $connection_provider, $context, $title);

        $this->display_title = '銀行清單';

        $search_type = 'bankname';

        if (
            isset($_GET['type']) &&
            in_array($_GET['type'], array('bankcode'))
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
            '<input class="cdx-radio__input" type="radio" name="type" value="bankname"' .
            (
                $search_type === 'bankname' ?
                ' checked' :
                ''
            ) .
            '>' .
            '<span class="cdx-radio__icon">' .
            '</span>' .
            '<div class="cdx-radio__label cdx-label">' .
            '<label for="type" class="cdx-label__label">' .
            '<span class="cdx-label__label__text">' .
            '銀行名稱' .
            '</span>' .
            '</label>' .
            '</div>' .
            '</div>' .
            '</div>' .
            '<div class="cdx-radio">' .
            '<div class="cdx-radio__wrapper">' .
            '<input class="cdx-radio__input" type="radio" name="type" value="bankcode"' .
            (
                $search_type === 'bankcode' ?
                ' checked' :
                ''
            ) .
            '>' .
            '<span class="cdx-radio__icon">' .
            '</span>' .
            '<div class="cdx-radio__label cdx-label">' .
            '<label for="type" class="cdx-label__label">' .
            '<span class="cdx-label__label__text">' .
            '銀行代碼' .
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

        $db_connection = mysqli_connect(
            'localhost',
            '113dbb04',
            '1763-1176',
            '113dbb04'
        );

        if ( mysqli_connect_errno() ) {
            $content = '<div class="cdx-message cdx-message--block cdx-message--error" role="alert">' .
                '<span class="cdx-message__icon">' .
                '</span>' .
                '<div class="cdx-message__content">' .
                '<p><strong>MySQL/MariaDB連線失敗。</strong></p>' .
                '<p>請稍後再試。</p>' .
                '</div>' .
                '</div>' .
                '<p>錯誤訊息：</p>' .
                mysqli_connect_error() .
                $content;

            $this->display_content = $content;

            return;
        }

        mysqli_query( $db_connection, 'SET NAMES utf8' );
        mysqli_query( $db_connection, 'SET CHARACTER_SET_CLIENT = utf8' );
        mysqli_query( $db_connection, 'SET CHARACTER_SET_RESULTS = utf8' );
        $sql = <<<SQL
SELECT bank_id, bank_code, bank_label_id, lt_label_id, lt_lang_id, lt_text
FROM bank INNER JOIN lang_text
ON bank_label_id = lt_label_id
WHERE lt_lang_id = 672
SQL;

        if (isset($_GET['q']) && trim($_GET['q']) !== '') {
            if ($search_type === 'bankname') {
                $sql .= ' AND lt_text LIKE \'%' .
                    str_replace( '\'', '\\\'', trim($_GET['q'])) .
                    '%\';';
            } elseif ($search_type === 'bankcode') {
                $sql .= ' AND bank_code LIKE \'' .
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

        $result_rows = mysqli_query($db_connection, $sql);

        if ($result_rows !== false) {
            $result_row = mysqli_fetch_row($result_rows);

            if ($result_row === null) {
                $content .= '<div class="cdx-message cdx-message--block cdx-message--warning">' .
                    '<span class="cdx-message__icon">' .
                    '</span>' .
                    '<div class="cdx-message__content">' .
                    '<p><strong>查無資料。</strong></p>' .
                    '<p>請嘗試其他搜尋條件。</p>' .
                    '</div>' .
                    '</div>';
            } else {
                $content .= '<div class="cdx-card-group">';

                while ($result_row !== null) {
                    $content .= '<a href="' .
                        $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                        $this->context->getUrlHost() .
                        '/' .
                        str_replace(
                            '$1',
                            'bank/' . $result_row[1] . '/',
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
                        'resources/assets/Bank_' . $result_row[0] . '.jpg' .
                        '&quot;);' .
                        '" class="cdx-thumbnail__image">' .
                        '</span>' .
                        '</span>' .
                        '<span class="cdx-card__text">' .
                        '<span class="cdx-card__text__title">' .
                        htmlspecialchars($result_row[5]) .
                        '</span>' .
                        '<span class="cdx-card__text__description">' .
                        '<!-- Description -->' .
                        '</span>' .
                        '<span class="cdx-card__text__supporting-text">' .
                        '銀行代碼：' . htmlspecialchars($result_row[1]) .
                        '</span>' .
                        '</span>' .
                        '</a>';

                    $result_row = mysqli_fetch_row($result_rows);
                }

                $content .= '</div>';
            }
        }

        $this->display_content = $content;
    }

    public function getListItems()
    {
    }
}
