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
        } elseif ($this->title->getCreatableUnit() !== '') {
            $this->has_content = false;

            return;
        } elseif ($this->title->getCreatableClub() !== '') {
            $this->has_content = false;

            return;
        } else {
            $this->display_title = '公告';
        }

        $search_type = 'all';

        if (
            isset($_GET['type']) &&
            in_array($_GET['type'], array('subject', 'content'))
        ) {
            $search_type = $_GET['type'];
        }

        $content = '<a class="' .
            'cdx-button ' .
            'cdx-button--fake-button ' .
            'cdx-button--fake-button--enabled ' .
            'cdx-button--weight-primary ' .
            'cdx-button--action-progressive' .
            '" ' .
            'tabindex="7"' .
            'href="' .
            $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
            $this->context->getUrlHost() .
            '/' .
            str_replace(
                '$1',
                (
                    $this->title->getUnitType() === 'club' ?
                    'club/' . $this->title->getUnit():
                    (
                        $this->title->getUnitType() === 'unit' ?
                        $this->title->getUnit() . '/' :
                        ''
                    )
                ) . 'announcement/new/',
                $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
            ) .
            '">' .
            '新增公告' .
            '</a>' .
            '<div class="' .
            'cdx-form-wrapper ' .
            'cdx-form-wrapper-padded ' .
            'cdx-form-wrapper-framed' .
            '">' .
            '<form method="get" action=".">' .
            '<div class="cdx-field">' .
            '<div class="cdx-label">' .
            '<label class="cdx-label__label" for="aid">' .
            '<span class="cdx-label__label__text">' .
            '公告編號' .
            '</span>' .
            '</label>' .
            '</div>' .
            '<div class="cdx-field__control">' .
            '<div class="cdx-search-input">' .
            '<div class="cdx-search-input__input-wrapper">' .
            '<div class="cdx-text-input cdx-text-input--has-start-icon">' .
            '<input class="cdx-text-input__input" type="search" name="aid" placeholder="搜尋公告編號"' .
            (
                isset( $_GET['aid'] ) ?
                ' value="' . trim( $_GET['aid'] ) . '"' :
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
            '<input class="cdx-radio__input" type="radio" name="type" value="all"' .
            (
                $search_type === 'all' ?
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
            '<input class="cdx-radio__input" type="radio" name="type" value="subject"' .
            (
                $search_type === 'subject' ?
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
            '<input class="cdx-radio__input" type="radio" name="type" value="content"' .
            (
                $search_type === 'content' ?
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
            '<input class="cdx-text-input__input" type="search" name="q" placeholder="搜尋文字"' .
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
            '</button>' .
            '</div>' .
            '</form>' .
            '</div>';

        $search_type_sql = '';
        $order_type = ' DESC';

        if ($this->title->getUnit() !== '') {
            $search_type_sql .= ' WHERE ui_unit_id = ' . $this->title->getUnitId();
        }

        if (isset($_GET['q']) && trim($_GET['q']) !== '') {
            if ($this->title->getUnit() !== '') {
                $search_type_sql .= ' AND';
            } else {
                $search_type_sql .= ' WHERE';
            }

            if ($search_type === 'all') {
                $search_type_sql .= ' lt_announcement_subject.lt_text LIKE \'%' .
                    str_replace( '\'', '\\\'', trim($_GET['q'])) .
                    '%\'';
            } elseif ($search_type === 'subject') {
                $search_type_sql .= ' (lt_announcement_subject.lt_text LIKE \'%' .
                    str_replace( '\'', '\\\'', trim($_GET['q'])) .
                    '%\' OR lt_announcement_subject.lt_text LIKE \'%' .
                    str_replace( '\'', '\\\'', trim($_GET['q'])) .
                    '%\')';
            } elseif ($search_type === 'content') {
                $search_type_sql .= ' lt_announcement_content.lt_text LIKE \'%' .
                    str_replace( '\'', '\\\'', trim($_GET['q'])) .
                    '%\'';
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
        } else {
            $search_type = false;
        }

        if (isset($_GET['aid']) && trim($_GET['aid']) !== '') {
            if (
                $this->title->getUnit() !== '' ||
                (isset($_GET['q']) && trim($_GET['q']) !== '')
            ) {
                $search_type_sql .= ' AND';
            } else {
                $search_type_sql .= ' WHERE';
            }

            $search_type_sql .= ' announcement_id LIKE \'' .
                str_replace('\'', '\\\'', trim($_GET['q'])) .
                '%\'';
            $order_type = ' ASC';
        }

        $search_type_sql .= ' ORDER BY announcement_id' . $order_type;

        $database = $this->connection_provider
            ->getDatabase();

        $result = $database
            ->newQuery(
                'SELECT ' .
                'announcement_id, ' .
                'announcement_created_timestamp, ' .
                'announcement_status, ' .
                'announcement_ui_id, ' .
                'ui_id, ' .
                'ui_user_id, ' .
                'user_id, ' .
                'user_status, ' .
                'user_name_label_id, ' .
                'lt_user_name.lt_label_id, ' .
                'lt_user_name.lt_lang_id, ' .
                'lt_user_name.lt_text, ' .
                'ui_unit_id, ' .
                'unit_id, ' .
                'unit_type, ' .
                'unit_code, ' .
                'unit_name_label_id, ' .
                'lt_unit_name.lt_label_id, ' .
                'lt_unit_name.lt_lang_id, ' .
                'lt_unit_name.lt_text, ' .
                'announcement_subject_label_id, ' .
                'lt_announcement_subject.lt_label_id, ' .
                'lt_announcement_subject.lt_lang_id, ' .
                'lt_announcement_subject.lt_text ' .
                'FROM announcement ' .
                'INNER JOIN user_identity ' .
                'ON ui_id = announcement_ui_id ' .
                'INNER JOIN user ' .
                'ON ui_user_id = user_id ' .
                'INNER JOIN lang_text AS lt_user_name ' .
                'ON lt_user_name.lt_label_id = user_name_label_id ' .
                'AND lt_user_name.lt_lang_id = 672 ' .
                'INNER JOIN unit ' .
                'ON unit_id = ui_unit_id ' .
                'INNER JOIN lang_text AS lt_unit_name ' .
                'ON lt_unit_name.lt_label_id = unit_name_label_id ' .
                'AND lt_unit_name.lt_lang_id = 672 ' .
                'INNER JOIN lang_text AS lt_announcement_subject ' .
                'ON lt_announcement_subject.lt_label_id = announcement_subject_label_id ' .
                'AND lt_announcement_subject.lt_lang_id = 672 ' .
                $search_type_sql .
                ';'
            )
            ->fetchResultSet();

        if (!$result->hasRows()) {
            if ($search_type !== false) {
                $content .= '<div class="cdx-message cdx-message--block cdx-message--warning">' .
                    '<span class="cdx-message__icon">' .
                    '</span>' .
                    '<div class="cdx-message__content">' .
                    '<p><strong>查無資料。</strong></p>' .
                    '<p>請嘗試其他搜尋條件。</p>' .
                    '</div>' .
                    '</div>';
            } else {
                $content .= '<div class="cdx-message cdx-message--block cdx-message--notice">' .
                    '<span class="cdx-message__icon">' .
                    '</span>' .
                    '<div class="cdx-message__content">' .
                    '<p>暫無公告。</p>' .
                    '</div>' .
                    '</div>';
            }
        } else {
            $row = $result->fetchRow();
            $content .= '<div class="cdx-card-group">';

            while ($row !== null) {
                $announcement_id = $row[0];
                $created_timestamp = $row[1];
                $status = $row[2];
                $user_identity_id = $row[3];
                $user_id = $row[5];
                $username = $row[11];
                $unit_id = $row[12];
                $unit_type = $row[14];
                $unit_prefix = '';
                $unit_code = $row[15];
                $unit_name = $row[19];
                $subject = $row[23];

                if ($unit_type === 2 || $unit_type === '2') {
                    $unit_prefix = 'club/';
                }

                $content .= '<a href="' .
                    $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                    $this->context->getUrlHost() .
                    '/' .
                    str_replace(
                        '$1',
                        $unit_prefix . $unit_code . '/' . 'announcement/' . $announcement_id . '/',
                        $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
                    ) .
                    '" class="cdx-card cdx-card--is-link">' .
                    '<span class="cdx-card__icon cdx-icon cdx-icon--medium">' .
                    '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 20 20" aria-hidden="true">' .
                    '<g>' .
                    '<path d="M19 16 2 12a3.83 3.83 0 01-1-2.5A3.83 3.83 0 012 7l17-4z" />' .
                    '<rect width="4" height="8" x="4" y="9" rx="2" />' .
                    '</g>' .
                    '</svg>' .
                    '</span>' .
                    '<span class="cdx-card__text">' .
                    '<span class="cdx-card__text__title">' .
                    htmlspecialchars($subject) .
                    '</span>' .
                    '<span class="cdx-card__text__description">' .
                    '<!-- Description -->' .
                    '</span>' .
                    '<span class="cdx-card__text__supporting-text">' .
                    '發布時間：' . htmlspecialchars($created_timestamp) . '<br />' .
                    (
                        $this->title->getUnit() === '' ?
                        '發布單位：' . htmlspecialchars($unit_name) . '<br />' :
                        ''
                    ) .
                    '發布者：' . htmlspecialchars($username) .
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
