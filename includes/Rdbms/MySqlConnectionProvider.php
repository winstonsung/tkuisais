<?php

namespace Isais\Rdbms;

use Isais\Config\MainConfigNames;
use Isais\Rdbms\MySqlQuery;

class MySqlConnectionProvider {
    private $config;

    private $database;

    public function __construct(
        $config
    ) {
        $this->config = $config;
    }

    public function getDatabase() {
        $connection = mysqli_connect(
            $this->config->getOption(MainConfigNames::CONFIG_DB_SERVER),
            $this->config->getOption(MainConfigNames::CONFIG_DB_USERNAME),
            $this->config->getOption(MainConfigNames::CONFIG_DB_PASSWORD),
            $this->config->getOption(MainConfigNames::CONFIG_DB_NAME),
            $this->config->getOption(MainConfigNames::CONFIG_DB_PORT)
        );

        if (mysqli_connect_errno()) {
            echo '<div class="' .
                'cdx-message cdx-message--block cdx-message--error' .
                '" role="alert">' .
                '<span class="cdx-message__icon">' .
                '</span>' .
                '<div class="cdx-message__content">' .
                '<p><strong>MySQL/MariaDB連線失敗。</strong></p>' .
                '<p>請稍後再試。</p>' .
                '</div>' .
                '</div>' .
                '<p>錯誤訊息：</p>' .
                mysqli_connect_error();
        }

        $this->database = $connection;

        return $this;
    }

    public function newQuery($query) {
        return new MySqlQuery(
            $this->database,
            $query
        );
    }
}
