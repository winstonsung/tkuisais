<?php

namespace Isais\Content;

use Isais\Config\MainConfigNames;
use Isais\Content\Content;

class BankContent extends Content {
    public function __construct($auth_manager, $config, $connection_provider, $context, $title)
    {
        parent::__construct($auth_manager, $config, $connection_provider, $context, $title);

        $bank_code = $this->title->getItem();
        $database = $this->connection_provider
            ->getDatabase();
        $result = $database
            ->newQuery(
                'SELECT ' .
                'bank_id, ' .
                'bank_code, ' .
                'bank_label_id, ' .
                'lt_bank_name.lt_label_id, ' .
                'lt_bank_name.lt_lang_id, ' .
                'lt_bank_name.lt_text ' .
                'FROM bank ' .
                'INNER JOIN lang_text AS lt_bank_name ' .
                'ON lt_bank_name.lt_label_id = bank_label_id ' .
                'AND lt_bank_name.lt_lang_id = 672 ' .
                'WHERE bank_code = \'' . $bank_code . '\''
            )
            ->fetchResultSet();

        if ($result->hasRows()) {
            $row = $result->fetchRow();
            $bank_id = $row[0];
            $bank_name = $row[5];

            $this->display_title = $bank_name;
            $this->display_content = '<div class="cdx-card">' .
                '<span class="cdx-thumbnail cdx-card__thumbnail">' .
                '<span style="' .
                'background-image: ' .
                'url(&quot;' .
                $this->config->getOption(MainConfigNames::CONFIG_PROTOCOL) .
                $this->context->getUrlHost() .
                '/' .
                $this->config->getOption(MainConfigNames::CONFIG_SCRIPT_PATH) .
                'resources/assets/Bank_' . $bank_id . '.jpg' .
                '&quot;);' .
                '" class="cdx-thumbnail__image">' .
                '</span>' .
                '</span>' .
                '<span class="cdx-card__text">' .
                '<span class="cdx-card__text__title">' .
                htmlspecialchars( $bank_name ) .
                '</span>' .
                '<span class="cdx-card__text__description">' .
                '<!-- Description -->' .
                '</span>' .
                '<span class="cdx-card__text__supporting-text">' .
                '銀行代碼：' . htmlspecialchars( $bank_code ) .
                '</span>' .
                '</span>' .
                '</div>';
        } else {
            $this->has_content = false;
        }
    }
}
