<?php

namespace Isais\Content;

use Isais\Config\MainConfigNames;
use Isais\Content\Content;

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
                $this->display_title = '新增' . $this->title->getUnitName() . '公告';

                if (
                    isset($params_post['subject']) &&
                    trim($params_post['subject']) !== '' &&
                    isset($params_post['content']) &&
                    trim($params_post['content']) !== ''
                ) {
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
                            '"' . $params_post['subject'] . '"' .
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
                                '"' . $params_post['content'] . '"' .
                                ');'
                            )
                            ->fetchResultSet();
                    $database
                        ->newQuery(
                            'INSERT INTO announcement (' .
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
                            'announcement_content_label_id, ' .
                            ') VALUES (' .
                            '"' . $created_timestamp . '", ' .
                            '1, ' .
                            '(SELECT MAX(lt.lt_label_id) FROM lang_text AS lt)' .
                            ');'
                        )
                        ->fetchResultSet();
                    $database
                        ->newQuery(
                            'INSERT INTO user_identity (' .
                            'ui_user_id, ' .
                            'ui_created_timestamp, ' .
                            'ui_status, ' .
                            'ui_scope, ' .
                            'ui_unit_id, ' .
                            'ui_code, ' .
                            'ui_role' .
                            ') VALUES (' .
                            'LAST_INSERT_ID(),' .
                            '"' . $created_timestamp . '", ' .
                            '1, ' .
                            '0, ' .
                            '0, ' .
                            '"' . $user_identity_code . '", ' .
                            '0' .
                            ');'
                        )
                        ->fetchResultSet();
                    $database
                        ->newQuery(
                            'COMMIT;'
                        )
                        ->fetchResultSet();
                }

                return;
            }

            $this->display_title = '新增公告';

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
                'WHERE announcement_id = \'' . $announcement_id . '\''
            )
            ->fetchResultSet();

        if ($result->hasRows()) {
            $row = $result->fetchRow();
            $announcement_created_timestamp = $row[1];
            $announcement_status = $row[2];
            $announcement_subject = $row[6];
            $announcement_content = $row[10];
            $announcement_user_identity_id = $row[11];

            $this->display_title = $announcement_subject;
            $this->display_content = $announcement_content;
        } else {
            $this->has_content = false;
        }
    }
}
