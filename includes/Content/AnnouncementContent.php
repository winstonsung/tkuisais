<?php

namespace Isais\Content;

use Isais\Config\MainConfigNames;
use Isais\Content\Content;
use Isais\Context\Context;

class AnnouncementContent extends Content {
    public static $status_types = array(
        '0' => 'draft',
        '1' => 'active',
        '2' => 'timestamp-based',
        '3' => 'outdated',
    );

    public function __construct($auth_manager, $config, $connection_provider, $context, $title)
    {
        parent::__construct($auth_manager, $config, $connection_provider, $context, $title);

        $params_post = $this->context->getUrlPostParameters();
        $announcement_id = $this->title->getItem();
        $database = $this->connection_provider
            ->getDatabase();

        if ($announcement_id === 'new') {
            if ($this->title->getUnit() !== '') {
                $this->display_title = '發布' . $this->title->getUnitName() . '公告';

                if (
                    isset($params_post['user_identity']) &&
                    trim($params_post['user_identity']) !== '' &&
                    isset($params_post['subject']) &&
                    trim($params_post['subject']) !== '' &&
                    isset($params_post['content']) &&
                    trim($params_post['content']) !== ''
                ) {
                    $result = $database
                        ->newQuery(
                            'SELECT ' .
                            'ui_id, ' .
                            'ui_user_id, ' .
                            'user_id, ' .
                            'user_status, ' .
                            'ui_status, ' .
                            'ui_scope, ' .
                            'ui_unit_id, ' .
                            'ui_code, ' .
                            'ui_role ' .
                            'FROM user_identity ' .
                            'INNER JOIN user ' .
                            'ON ui_user_id = user_id ' .
                            'WHERE ui_id = ' . trim($params_post['user_identity']) . ' ' .
                            'AND ui_user_id = ' . $this->auth_manager->getUserId() . ' ' .
                            'AND user_status = 1 ' .
                            'AND ui_status = 1 ' .
                            (
                                $this->title->getUnit() === '' ?
                                '' :
                                'AND ui_unit_id = ' . $this->title->getUnitId()
                            ) .
                            ';'
                        )
                        ->fetchResultSet();

                    if ($result->hasRows()) {
                        $row = $result->fetchRow();

                        $current_time = strtotime("now");
                        $created_timestamp = gmdate(
                            'Y-m-d\TH:i:s\Z',
                            $current_time
                        );

                        $database
                            ->newQuery(
                                'START TRANSACTION;'
                            )
                            ->fetchResultSet();
                        $database
                            ->newQuery(
                                'INSERT INTO lang_text (' .
                                'lt_label_id, ' .
                                'lt_lang_id, ' .
                                'lt_text' .
                                ') ' .
                                'VALUES (' .
                                '(SELECT MAX(lt.lt_label_id) FROM lang_text AS lt) + 1, ' .
                                '672, ' .
                                '"' . trim($params_post['subject']) . '"' .
                                ');'
                            )
                            ->fetchResultSet();
                        $database
                            ->newQuery(
                                'INSERT INTO lang_text (' .
                                'lt_label_id, ' .
                                'lt_lang_id, ' .
                                'lt_text' .
                                ') ' .
                                'VALUES (' .
                                '(SELECT MAX(lt.lt_label_id) FROM lang_text AS lt) + 1, ' .
                                '672, ' .
                                '"' . trim($params_post['content']) . '"' .
                                ');'
                            )
                            ->fetchResultSet();
                        $database
                            ->newQuery(
                                'INSERT INTO announcement (' .
                                'announcement_created_timestamp, ' .
                                'announcement_status, ' .
                                'announcement_ui_id, ' .
                                'announcement_subject_label_id, ' .
                                'announcement_content_label_id' .
                                ') VALUES (' .
                                '"' . $created_timestamp . '", ' .
                                '1, ' .
                                trim($params_post['user_identity']) . ', ' .
                                '(SELECT MAX(lt_label_id) FROM lang_text) - 1,' .
                                '(SELECT MAX(lt_label_id) FROM lang_text)' .
                                ');'
                            )
                            ->fetchResultSet();
                        $result = $database
                            ->newQuery(
                                'SELECT ' .
                                'announcement_id, ' .
                                'announcement_ui_id, ' .
                                'ui_id, ' .
                                'ui_user_id, ' .
                                'ui_unit_id, ' .
                                'unit_id, ' .
                                'unit_type, ' .
                                'unit_code ' .
                                'FROM announcement ' .
                                'INNER JOIN user_identity ' .
                                'ON announcement_ui_id = ui_id  ' .
                                'INNER JOIN unit ' .
                                'ON ui_unit_id = unit_id ' .
                                'WHERE announcement_id = LAST_INSERT_ID();'
                            )
                            ->fetchResultSet();
                        $database
                            ->newQuery(
                                'COMMIT;'
                            )
                            ->fetchResultSet();

                        if ($result->hasRows()) {
                            $row = $result->fetchRow();
                            $announcement_id = $row[0];
                            $unit_type = $row[6];
                            $unit_code = $row[7];
                            $unit_prefix = '';

                            if ($unit_type === 2 || $unit_type === '2') {
                                $unit_prefix = 'club/';
                            }

                            $response_headers = array_merge(
                                $this->context->getResponseHeaders(),
                                array(
                                    'http_status' => $context->getServerProtocal() . ' 302 Found',
                                    'location' => $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                                        $this->context->getUrlHost() .
                                        '/' .
                                        str_replace(
                                            '$1',
                                            $unit_prefix . $unit_code . '/' . 'announcement/' . $announcement_id . '/',
                                            $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
                                        ),
                                )
                            );

                            $this->context->setContext(
                                Context::CONTEXT_RESPONSE_HEADERS,
                                $response_headers
                            );

                            $this->display_title = '已發布公告';
                            $this->display_content = '<p>已發布公告「' .
                                trim($params_post['subject']) .
                                '」。</p>' .
                                '<p>正在重新導向……</p>';

                            return;
                        }
                    } else {
                        $this->display_content = '<div class="' .
                            'cdx-message ' .
                            'cdx-message--block ' .
                            'cdx-message--error' .
                            '" role="alert">' .
                            '<span class="cdx-message__icon">' .
                            '</span>' .
                            '<div class="cdx-message__content">' .
                            '<p><strong>錯誤：非單位成員。</strong></p>' .
                            '<p>請重新確認權限。</p>' .
                            '</div>' .
                            '</div>';
                    }
                }
            } else {
                $this->display_title = '發布公告';
            }

            $ui_options = '';

            if ($this->auth_manager->isLoggedIn()) {
                $result = $database
                    ->newQuery(
                        'SELECT ' .
                        'ui_id, ' .
                        'ui_user_id, ' .
                        'user_id, ' .
                        'user_status, ' .
                        'user_name_label_id, ' .
                        'lt_user_name.lt_label_id, ' .
                        'lt_user_name.lt_lang_id, ' .
                        'lt_user_name.lt_text, ' .
                        'ui_status, ' .
                        'ui_scope, ' .
                        'ui_unit_id, ' .
                        'unit_id, ' .
                        'unit_type, ' .
                        'unit_code, ' .
                        'unit_name_label_id, ' .
                        'lt_unit_name.lt_label_id, ' .
                        'lt_unit_name.lt_lang_id, ' .
                        'lt_unit_name.lt_text, ' .
                        'ui_code, ' .
                        'ui_role ' .
                        'FROM user_identity ' .
                        'INNER JOIN user ' .
                        'ON ui_user_id = user_id ' .
                        'INNER JOIN lang_text AS lt_user_name ' .
                        'ON lt_user_name.lt_label_id = user_name_label_id ' .
                        'AND lt_user_name.lt_lang_id = 672 ' .
                        'INNER JOIN unit ' .
                        'ON ui_unit_id = unit_id ' .
                        'INNER JOIN lang_text AS lt_unit_name ' .
                        'ON lt_unit_name.lt_label_id = unit_name_label_id ' .
                        'AND lt_unit_name.lt_lang_id = 672 ' .
                        'WHERE ui_user_id = ' . $this->auth_manager->getUserId() . ' ' .
                        'AND ui_status = 1 ' .
                        'AND user_status = 1 ' .
                        (
                            $this->title->getUnit() === '' ?
                            '' :
                            'AND ui_unit_id = ' . $this->title->getUnitId()
                        ) .
                        ';'
                    )
                    ->fetchResultSet();

                if ($result->hasRows()) {
                    $row = $result->fetchRow();

                    while ($row !== null) {
                        $ui_id = $row[0];
                        $username = $row[7];
                        $unit_code = $row[13];
                        $unit_name = $row[17];
                        $ui_code = $row[18];
                        $ui_options .= '<option value="' . $ui_id . '">' .
                            (
                                $this->title->getUnit() === '' ?
                                $unit_code . ' ' . $unit_name . ' ' :
                                ''
                            ) .
                            $ui_code . ' ' . $username .
                            '</option>';

                        $row = $result->fetchRow();
                    }
                }
            }

            $this->display_content .= '<div class="cdx-form-wrapper">' .
                '<form method="post" action=".">' .
                '<div class="cdx-field">' .
                '<div class="cdx-label">' .
                '<label class="cdx-label__label" for="subject">' .
                '<span class="cdx-label__label__text">' .
                (
                    $this->title->getUnit() === '' ?
                    '單位及身分' :
                    '身分'
                ) .
                '</span>' .
                '</label>' .
                // '<span class="cdx-label__description">' .
                // '</span>' .
                '</div>' .
                '<div class="cdx-field__control">' .
                '<select class="cdx-select" name="user_identity"' .
                (
                    $ui_options === '' ?
                    ' disabled' :
                    ' required'
                ) .
                '>' .
                (
                    $ui_options === '' ?
                    '<option value="">無法選擇</option>' :
                    (
                        $this->title->getUnit() === '' ?
                        '<option value="">請選擇要發布公告的單位及身分</option>' :
                        '<option value="">請選擇要發布公告的身分</option>'
                    )
                ) .
                $ui_options .
                '</select>' .
                '</div>' .
                (
                    $ui_options === '' ?
                    '<div class="cdx-field__help-text">' .
                        '<div role="alert" class="cdx-message cdx-message--inline cdx-message--error">' .
                        '<span class="cdx-icon cdx-icon--medium cdx-message__icon--vue">' .
                        '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 20 20" aria-hidden="true">' .
                        '<g>' .
                        '<path d="M13.728 1H6.272L1 6.272v7.456L6.272 19h7.456L19 13.728V6.272zM11 15H9v-2h2zm0-4H9V5h2z" />' .
                        '</g>' .
                        '</svg>' .
                        '</span>' .
                        '<div class="cdx-message__content">' .
                        (
                            $this->auth_manager->isLoggedIn() ?
                            (
                                $this->title->getUnit() === '' ?
                                '<p>您目前沒有發布公告的權限。</p>' :
                                '<p>您目前沒有發布此單位公告的權限。</p>'
                            ) :
                            '<p>請先<a href="' .
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
                                '">登入</a>再發布公告。</p>'
                        ) .
                        '</div>' .
                        '</div>' .
                        '</div>':
                    ''
                ) .
                '</div>' .
                '<div class="cdx-field">' .
                '<div class="cdx-label">' .
                '<label class="cdx-label__label" for="subject">' .
                '<span class="cdx-label__label__text">' .
                '標題' .
                '</span>' .
                '</label>' .
                '<span class="cdx-label__description">' .
                '簡明扼要說明目的與期望，以不超過60個字為原則。' .
                '</span>' .
                '</div>' .
                '<div class="cdx-field__control">' .
                '<div class="cdx-text-input cdx-text-input--status-default">' .
                '<input class="cdx-text-input__input" name="subject" type="text" size="255" ' .
                'placeholder="請輸入公告標題"' .
                (
                    $ui_options === '' ?
                    ' disabled' :
                    ' required'
                ) .
                '' .
                (
                    isset($params_post['subject']) && trim($params_post['subject']) !== '' ?
                    ' value="' . htmlspecialchars(trim($params_post['subject'])) . '"' :
                    ''
                ) .
                '>' .
                '</div>' .
                '</div>' .
                // '<div class="cdx-field__help-text">' .
                // '</div>' .
                '</div>' .
                '<div class="cdx-field">' .
                '<div class="cdx-label">' .
                '<label class="cdx-label__label" for="content">' .
                '<span class="cdx-label__label__text">' .
                '內容' .
                '</span>' .
                '</label>' .
                // '<span class="cdx-label__description">' .
                // '</span>' .
                '</div>' .
                '<div class="cdx-field__control">' .
                '<div class="cdx-text-area cdx-text-area--status-default">' .
                '<textarea class="cdx-text-area__textarea cdx-text-area__textarea--is-autosize cdx-text-area__textarea--content-input" ' .
                'name="content" placeholder="請輸入公告內容"' .
                (
                    $ui_options === '' ?
                    ' disabled' :
                    ' required'
                ) .
                '>' .
                (
                    isset($params_post['content']) && trim($params_post['content']) !== '' ?
                    htmlspecialchars(trim($params_post['content'])) :
                    ''
                ) .
                '</textarea>' .
                '</div>' .
                '</div>' .
                // '<div class="cdx-field__help-text">' .
                // '</div>' .
                '</div>' .
                '<div class="cdx-field">' .
                '<button type="submit" class="' .
                'cdx-button ' .
                'cdx-button--action-progressive ' .
                'cdx-button--weight-primary' .
                '"' .
                (
                    $ui_options === '' ?
                    ' disabled' :
                    ' required'
                ) .
                '>' .
                '發布公告' .
                '</button>' .
                '</div>' .
                '</form>' .
                '</div>';

            return;
        }

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
                'lt_announcement_subject.lt_text, ' .
                'announcement_content_label_id, ' .
                'lt_announcement_content.lt_label_id, ' .
                'lt_announcement_content.lt_lang_id, ' .
                'lt_announcement_content.lt_text ' .
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
                'INNER JOIN lang_text AS lt_announcement_content ' .
                'ON lt_announcement_content.lt_label_id = announcement_content_label_id ' .
                'AND lt_announcement_content.lt_lang_id = 672 ' .
                'WHERE announcement_id = \'' . $announcement_id . '\''
            )
            ->fetchResultSet();

        if ($result->hasRows()) {
            $row = $result->fetchRow();
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
            $subject_label_id = $row[20];
            $subject = $row[23];
            $content_label_id = $row[24];
            $content = $row[27];

            if ($unit_type === 2 || $unit_type === '2') {
                $unit_prefix = 'club/';
            }

            $params_all = $this->context->getUrlAllParameters();
            $params_post = $this->context->getUrlPostParameters();

            if (isset($params_all['action'])) {
                if ($params_all['action'] === 'edit') {
                    if (
                        isset($params_post['subject']) &&
                        trim($params_post['subject']) !== '' &&
                        isset($params_post['content']) &&
                        trim($params_post['content']) !== ''
                    ) {
                        $database
                            ->newQuery(
                                'UPDATE lang_text ' .
                                'SET lt_text = \'' .
                                trim($params_post['subject']) .
                                '\'' .
                                'WHERE lt_label_id = ' . $subject_label_id . ' ' .
                                'AND lt_lang_id = 672 ' .
                                ';'
                            )
                            ->fetchResultSet();
                        $database
                            ->newQuery(
                                'UPDATE lang_text ' .
                                'SET lt_text = \'' .
                                trim($params_post['content']) .
                                '\'' .
                                'WHERE lt_label_id = ' . $content_label_id . ' ' .
                                'AND lt_lang_id = 672 ' .
                                ';'
                            )
                            ->fetchResultSet();

                        $response_headers = array_merge(
                            $this->context->getResponseHeaders(),
                            array(
                                'http_status' => $context->getServerProtocal() . ' 302 Found',
                                'location' => $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                                    $this->context->getUrlHost() .
                                    '/' .
                                    str_replace(
                                        '$1',
                                        $unit_prefix . $unit_code . '/' . 'announcement/' . $announcement_id . '/',
                                        $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
                                    ),
                            )
                        );

                        $this->context->setContext(
                            Context::CONTEXT_RESPONSE_HEADERS,
                            $response_headers
                        );

                        return;
                    }

                    $this->display_title = '編輯公告';
                    $this->display_content .= '<div class="cdx-form-wrapper">' .
                        '<form method="post" action="' .
                        $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                        $this->context->getUrlHost() .
                        '/' .
                        str_replace(
                            '$1',
                            $unit_prefix . $unit_code . '/' . 'announcement/' . $announcement_id . '/' .
                                '?action=edit',
                            $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
                        ) .
                        '">' .
                        '<div class="cdx-field">' .
                        '<div class="cdx-label">' .
                        '<label class="cdx-label__label" for="subject">' .
                        '<span class="cdx-label__label__text">' .
                        '標題' .
                        '</span>' .
                        '</label>' .
                        '<span class="cdx-label__description">' .
                        '簡明扼要說明目的與期望，以不超過60個字為原則。' .
                        '</span>' .
                        '</div>' .
                        '<div class="cdx-field__control">' .
                        '<div class="cdx-text-input cdx-text-input--status-default">' .
                        '<input class="cdx-text-input__input" name="subject" type="text" size="255" ' .
                        'placeholder="請輸入公告標題"' .
                        ' required' .
                        ' value="' . $subject . '"' .
                        '>' .
                        '</div>' .
                        '</div>' .
                        // '<div class="cdx-field__help-text">' .
                        // '</div>' .
                        '</div>' .
                        '<div class="cdx-field">' .
                        '<div class="cdx-label">' .
                        '<label class="cdx-label__label" for="content">' .
                        '<span class="cdx-label__label__text">' .
                        '內容' .
                        '</span>' .
                        '</label>' .
                        // '<span class="cdx-label__description">' .
                        // '</span>' .
                        '</div>' .
                        '<div class="cdx-field__control">' .
                        '<div class="cdx-text-area cdx-text-area--status-default">' .
                        '<textarea class="cdx-text-area__textarea cdx-text-area__textarea--is-autosize cdx-text-area__textarea--content-input" ' .
                        'name="content" placeholder="請輸入公告內容"' .
                        ' required' .
                        '>' .
                        $content .
                        '</textarea>' .
                        '</div>' .
                        '</div>' .
                        // '<div class="cdx-field__help-text">' .
                        // '</div>' .
                        '</div>' .
                        '<div class="cdx-field">' .
                        '<button type="submit" class="' .
                        'cdx-button ' .
                        'cdx-button--action-progressive ' .
                        'cdx-button--weight-primary' .
                        '">' .
                        '更新公告' .
                        '</button>' .
                        '</div>' .
                        '</form>' .
                        '</div>';

                    return;
                } elseif ($params_all['action'] === 'delete') {
                    if (isset($params_post['delete']) && $params_post['delete'] === '1') {
                        $database
                            ->newQuery(
                                'DELETE FROM announcement ' .
                                'WHERE announcement_id = ' . $announcement_id . ';'
                            )
                            ->fetchResultSet();
                        $database
                            ->newQuery(
                                'DELETE FROM lang_text ' .
                                'WHERE lt_label_id = ' . $subject_label_id . ';'
                            )
                            ->fetchResultSet();
                        $database
                            ->newQuery(
                                'DELETE FROM lang_text ' .
                                'WHERE lt_label_id = ' . $content_label_id . ';'
                            )
                            ->fetchResultSet();

                        $response_headers = array_merge(
                            $this->context->getResponseHeaders(),
                            array(
                                'http_status' => $context->getServerProtocal() . ' 302 Found',
                                'location' => $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                                    $this->context->getUrlHost() .
                                    '/' .
                                    str_replace(
                                        '$1',
                                        $unit_prefix . $unit_code . '/' . 'announcement/',
                                        $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
                                    ),
                            )
                        );

                        $this->context->setContext(
                            Context::CONTEXT_RESPONSE_HEADERS,
                            $response_headers
                        );

                        return;
                    }

                    $this->display_title = '刪除公告';
                    $this->display_content .= '<div id="user-logout-form" class="cdx-form-wrapper">' .
                        '<form class="cdx-form" method="post" action="' .
                        $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                        $this->context->getUrlHost() .
                        '/' .
                        str_replace(
                            '$1',
                            $unit_prefix . $unit_code . '/' . 'announcement/' . $announcement_id . '/' .
                                '?action=delete',
                            $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
                        ) .
                        '">' .
                        '<p>您是否確定要刪除公告「' .
                        $subject .
                        '」？</p>' .
                        '<input type="hidden" name="user" value="' .
                        $this->auth_manager->getUserId() .
                        '" />' .
                        '<input type="hidden" name="announcement_id" value="' .
                        $announcement_id .
                        '" />' .
                        '<div class="cdx-field">' .
                        '<div class="cdx-field__control">' .
                        '<button id="user-logout-button" ' .
                        'class="' .
                        'cdx-button ' .
                        'cdx-button--weight-primary ' .
                        'cdx-button--action-destructive ' .
                        '" name="delete" value="1" type="submit" ' .
                        'tabindex="6"' .
                        '>' .
                        '刪除' .
                        '</button>' .
                        '<a class="' .
                        'cdx-button ' .
                        'cdx-button--fake-button ' .
                        'cdx-button--fake-button--enabled' .
                        '" ' .
                        'tabindex="7"' .
                        'href="' .
                        $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                        $this->context->getUrlHost() .
                        '/' .
                        str_replace(
                            '$1',
                            $unit_prefix . $unit_code . '/' . 'announcement/' . $announcement_id . '/',
                            $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
                        ) .
                        '">' .
                        '取消' .
                        '</a>' .
                        '</div>' .
                        '</div>' .
                        '</form>' .
                        '</div>';

                    return;
                }
            }

            if ($this->title->getUnitId() !== $unit_id) {
                $response_headers = array_merge(
                    $this->context->getResponseHeaders(),
                    array(
                        'http_status' => $context->getServerProtocal() . ' 302 Found',
                        'location' => $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                            $this->context->getUrlHost() .
                            '/' .
                            str_replace(
                                '$1',
                                $unit_prefix . $unit_code . '/' . 'announcement/' . $announcement_id . '/',
                                $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
                            ),
                    )
                );

                $this->context->setContext(
                    Context::CONTEXT_RESPONSE_HEADERS,
                    $response_headers
                );
            }

            $this->display_title = $subject;
            if ($user_id === $this->auth_manager->getUserId()) {
                $this->display_content .= '<div class="actions cdx-field__control">' .
                    '<a class="' .
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
                        $unit_prefix . $unit_code . '/' . 'announcement/' . $announcement_id . '/' .
                            '?action=edit',
                        $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
                    ) .
                    '">' .
                    '編輯' .
                    '</a>' .
                    '<a class="' .
                    'cdx-button ' .
                    'cdx-button--fake-button ' .
                    'cdx-button--fake-button--enabled ' .
                    'cdx-button--weight-primary ' .
                    'cdx-button--action-destructive' .
                    '" ' .
                    'tabindex="7"' .
                    'href="' .
                    $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                    $this->context->getUrlHost() .
                    '/' .
                    str_replace(
                        '$1',
                        $unit_prefix . $unit_code . '/' . 'announcement/' . $announcement_id . '/' .
                            '?action=delete',
                        $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
                    ) .
                    '">' .
                    '刪除' .
                    '</a>' .
                    '</div>' .
                    '<hr />';
            }

            $this->display_content .= '<div class="announcement-info">' .
                '<p>發布者：' .
                '<a href="' .
                $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                $this->context->getUrlHost() .
                '/' .
                str_replace(
                    '$1',
                    $unit_prefix . $unit_code . '/',
                    $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
                ) .
                '">' .
                $unit_name .
                '</a>' .
                '　' .
                '<a href="' .
                $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                $this->context->getUrlHost() .
                '/' .
                str_replace(
                    '$1',
                    'user/' . $user_id . '/',
                    $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
                ) .
                '">' .
                $username .
                '</a>' .
                '</p>' .
                '</div>' .
                '<hr />' .
                '<div class="announcement-content">' .
                $content .
                '</div>';
        } else {
            $this->has_content = false;
        }
    }
}
