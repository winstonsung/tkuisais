<?php

namespace Isais\EntryPoint;

use Isais\Config\MainConfigNames;
use Isais\Context\Context;
use Isais\EntryPoint\EntryPoint;

class ApiEntryPoint extends EntryPoint
{
    public function execute()
    {
        $content_type = 'text/html';
        $content = '';
        $title = $this->context->getTitleFullText();
        $params_all = $this->context->getUrlAllParameters();
        $skin = null;

        if (
            isset($params_all['content_type']) &&
            in_array(
                $params_all['content_type'],
                array('json', 'xml')
            )
        ) {
            if ($params_all['content_type'] === 'json') {
                $content_type = 'application/json';
            } elseif ($params_all['content_type'] === 'xml') {
                $content_type = 'text/xml';
            }
        } else {
            $skin = $this->services->getSkin();
            $content = '<!DOCTYPE html>' .
            '<html lang="zh-Hant-TW" dir="ltr">' .
            '<head>' .
            '<title>' .
            'iSAIS校務資訊系統API | iSAIS校務資訊系統' .
            '</title>' .
            $skin->getHeadMetaTags() .
            $skin->getHeadLinkTags() .
            $skin->getHeadStylesheets() .
            $skin->getHeadScripts() .
            '</head>' .
            '<body class="site-main">';
        }

        if (in_array($title, array('announcement/'))) {
            $content .= '<h1>iSAIS校務資訊系統API結果</h1>' .
                '<div class="api-pretty-header">' .
                '<p>這是JSON格式的HTML實現。HTML對除錯很有用，但不適合應用程式使用。</p>' .
                '<p>指定<var>content_type</var>參數以變更輸出格式。</p>' .
                '<ul>' .
                '<li>要查看JSON格式的非HTML實現，設定<kbd>content_type=json</kbd>。</li>' .
                '<li>要查看XML格式的Atom實現，設定<kbd>content_type=xml</kbd>。</li>' .
                '</ul>' .
                '</div>';

            if ($title === 'announcement/') {
                $database = $this->services->getConnectionProvider()
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
                        'ORDER BY announcement_id DESC' .
                        ';'
                    )
                    ->fetchResultSet();

                if ($content_type === 'application/json') {
                    $content = '{' .
                        '"title":"iSAIS校務資訊系統 - 最新消息 [zh-Hant-TW]",' .
                        '"generator":"iSAIS 0.1.0",' .
                        '"entries":[';
                } elseif ($content_type === 'text/xml') {
                    $content = '<feed xmlns="http://www.w3.org/2005/Atom" xml:lang="zh-Hant-TW">' .
                        '<id></id>' .
                        '<title>iSAIS校務資訊系統 - 最新消息 [zh-Hant-TW]</title>' .
                        '<link rel="self" type="application/atom+xml" href="' .
                        $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                        $this->context->getUrlHost() . '/' .
                        $this->config->getOption(MainConfigNames::CONFIG_API_SCRIPT) . '/' .
                        'announcement/' .
                        '" />' .
                        '<link rel="alternate" type="text/html" href="' .
                        $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                        $this->context->getUrlHost() .
                        '/' .
                        str_replace(
                            '$1',
                            'announcement/',
                            $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
                        ) .
                        '" />' .
                        '<updated>2025-01-03T04:00:28Z</updated>' .
                        '<subtitle></subtitle>' .
                        '<generator>iSAIS 0.1.0</generator>';
                } else {
                    $content .= "<pre>\n" .
                        "{\n" .
                        "    \"title\": \"iSAIS校務資訊系統 - 最新消息 [zh-Hant-TW]\",\n" .
                        "    \"generator\": \"iSAIS 0.1.0\",\n" .
                        "    \"entries\": [";
                }

                $first_item = true;

                if ($result->hasRows()) {
                    $row = $result->fetchRow();

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
                        $announcement_subject = $row[23];
                        $announcement_content = $row[27];

                        if ($unit_type === 2 || $unit_type === '2') {
                            $unit_prefix = 'club/';
                        }

                        if ($content_type === 'application/json') {
                            if (!$first_item) {
                                $content .= ',';
                            }

                            $content .= '{' .
                                '"id":"' .
                                $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                                $this->context->getUrlHost() .
                                '/' .
                                str_replace(
                                    '$1',
                                    $unit_prefix . $unit_code . '/' . 'announcement/' . $announcement_id . '/',
                                    $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
                                ) .
                                '",' .
                                '"title":"' . $announcement_subject . '",' .
                                '"updated":"' . $created_timestamp . '",' .
                                '"summary_type":"html",' .
                                '"summary":"' . htmlspecialchars($announcement_content) . '",' .
                                '"author":{"name":"' . $username . '"}' .
                                '}';
                        } elseif ($content_type === 'text/xml') {
                            $content .= '<entry>' .
                                '<id>' .
                                $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                                $this->context->getUrlHost() .
                                '/' .
                                str_replace(
                                    '$1',
                                    $unit_prefix . $unit_code . '/' . 'announcement/' . $announcement_id . '/',
                                    $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
                                ) .
                                '</id>' .
                                '<title>' . $announcement_subject . '</title>' .
                                '<updated>' . $created_timestamp . '</updated>' .
                                '<summary type="html">' . htmlspecialchars($announcement_content) . '</summary>' .
                                '<author><name>'  . $username . '</name></author>' .
                                '</entry>';
                        } else {
                            if ($first_item) {
                                $content .= "\n";
                            } else {
                                $content .= ",\n";
                            }

                            $content .= "        {\n" .
                                "            \"id\": \"" .
                                $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                                $this->context->getUrlHost() .
                                '/' .
                                str_replace(
                                    '$1',
                                    $unit_prefix . $unit_code . '/' . 'announcement/' . $announcement_id . '/',
                                    $this->config->getOption(MainConfigNames::CONFIG_PAGE_PATH)
                                ) .
                                "\",\n" .
                                "            \"title\": \"" . $announcement_subject . "\",\n" .
                                "            \"updated\": \"" . $created_timestamp . "\",\n" .
                                "            \"summary_type\": \"html\",\n" .
                                "            \"summary\": \"" . htmlspecialchars($announcement_content) . "\",\n" .
                                "            \"author\": {\n" .
                                "                \"name\": \"" . $username . "\"\n" .
                                "            }\n" .
                                "        }";
                        }

                        $first_item = false;
                        $row = $result->fetchRow();
                    }
                }

                if ($content_type === 'application/json') {
                    $content .= ']}';
                } elseif ($content_type === 'text/xml') {
                    $content .= '</feed>';
                } else {
                    $content .= "\n" .
                        "    ]\n" .
                        "}\n" .
                        "</pre>";
                }
            }
        } else {
            $content .= '<h1>iSAIS校務資訊系統API說明</h1>'.
                '<p>此頁為自動產生的iSAIS校務資訊系統API說明文件頁面。</p>' .
                '<h2>內容類型</h2>' .
                '<kbd>content_type=</kbd>' .
                '<ul>' .
                '<li><kbd>HTML</kdb></li>' .
                '<li><kbd>JSON</kdb></li>' .
                '<li><kbd>XML</kdb></li>' .
                '</ul>' .
                '<h2>主要模組</h2>' .
                '<ul>' .
                '<li>' .
                '<a href="' .
                $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                $this->context->getUrlHost() . '/' .
                $this->config->getOption(MainConfigNames::CONFIG_API_SCRIPT) . '/' .
                'announcement/' .
                '"><kbd>announcement<kbd></a>：公告</li>' .
                '</ul>';
        }

        if ($content_type === 'text/html') {
            $content .= '</body>' .
                '</html>';
        }

        $response_headers = array_merge(
            $this->context->getResponseHeaders(),
            array(
                'Cache-Control' => 'private, must-revalidate, max-age=0',
                'Content-Length' => strlen($content),
                'Content-Type' => $content_type . '; charset=utf-8',
                'X-Content-Type-Options' => 'nosniff',
            )
        );

        $this->context->setContext(
            Context::CONTEXT_RESPONSE_HEADERS,
            $response_headers
        );
        parent::setResponseHeaders();

        echo $content;
    }
}
