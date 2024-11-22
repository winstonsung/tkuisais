<?php

namespace Isais\Content;

use Isais\Config\MainConfigNames;
use Isais\Content\Content;

class UnitContent extends Content {
    public function __construct($auth_manager, $config, $connection_provider, $context, $title)
    {
        parent::__construct($auth_manager, $config, $connection_provider, $context, $title);

        if ($this->title->getCreatableUnit() !== '') {
            $this->has_content = false;

            return;
        } elseif ($this->title->getCreatableClub() !== '') {
            $this->has_content = false;

            return;
        } elseif ($this->title->getUnit() === '') {
            $this->has_content = false;

            return;
        }

        $unit_name = $this->title->getUnitName();

        $this->display_title = $unit_name;
        $announcement_block = '';

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
                'ui_unit_id, ' .
                'unit_id, ' .
                'unit_type, ' .
                'unit_code, ' .
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
                'INNER JOIN unit ' .
                'ON unit_id = ui_unit_id ' .
                'INNER JOIN lang_text AS lt_announcement_subject ' .
                'ON lt_announcement_subject.lt_label_id = announcement_subject_label_id ' .
                'AND lt_announcement_subject.lt_lang_id = 672 ' .
                'INNER JOIN lang_text AS lt_announcement_content ' .
                'ON lt_announcement_content.lt_label_id = announcement_content_label_id ' .
                'AND lt_announcement_content.lt_lang_id = 672 ' .
                'WHERE ui_unit_id = ' . $this->title->getUnitId() .
                ';'
            )
            ->fetchResultSet();

        if (!$result->hasRows()) {
            $announcement_block .= '<div class="cdx-message cdx-message--block cdx-message--notice">' .
                '<span class="cdx-message__icon">' .
                '</span>' .
                '<div class="cdx-message__content">' .
                '<p>暫無公告。</p>' .
                '</div>' .
                '</div>';
        } else {
            $row = $result->fetchRow();
            $announcement_block .= '<div class="cdx-card-group">';

            while ($row !== null) {
                $announcement_id = $row[0];
                $announcement_created_timestamp = $row[1];
                $announcement_status = $row[2];
                $announcement_user_identity_id = $row[3];
                $announcement_user_id = $row[5];
                $unit_type = $row[8];
                $unit_prefix = '';
                $unit_code = $row[9];
                $announcement_subject = $row[13];

                if ($unit_type === 2 || $unit_type === '2') {
                    $unit_prefix = 'club/';
                }

                $announcement_block .= '<a href="' .
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
                    htmlspecialchars($announcement_subject) .
                    '</span>' .
                    '<span class="cdx-card__text__description">' .
                    '<!-- Description -->' .
                    '</span>' .
                    '<span class="cdx-card__text__supporting-text">' .
                    '發布時間：' . htmlspecialchars($announcement_created_timestamp) .
                    '</span>' .
                    '</span>' .
                    '</a>';

                $row = $result->fetchRow();
            }

            $announcement_block .= '</div>';
        }

        $this->display_content = '<h2>最新公告</h2>' .
            $announcement_block .
            '<p>' .
            '<a href="' .
            $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
            $this->context->getUrlHost() .
            '/' .
            str_replace(
                '$1',
                $unit_prefix . $unit_code . '/' . 'announcement/',
                $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
            ) .
            '">' .
            '查看所有公告 →' .
            '</a>' .
            '</p>';

        // $this->display_content = '<h2>最新公告</h2>' .
        //     '<h2>近期活動</h2>';
    }
}
