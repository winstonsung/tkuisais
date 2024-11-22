<?php

namespace Isais\Rdbms;

class MySqlQuery {
    private $database;

    private $query;

    private $result_rows;

    public function __construct(
        $database,
        $query
    ) {
        $this->database = $database;
        $this->query = $query;
    }

    public function fetchResultSet() {
        mysqli_query($this->database, 'SET NAMES utf8');
        mysqli_query($this->database, 'SET CHARACTER_SET_CLIENT = utf8');
        mysqli_query($this->database, 'SET CHARACTER_SET_RESULTS = utf8');

        $this->result_rows = mysqli_query($this->database, $this->query);

        if ($this->result_rows === false) {
            echo '<div class="' .
                'cdx-message cdx-message--block cdx-message--error' .
                '" role="alert">' .
                '<span class="cdx-message__icon">' .
                '</span>' .
                '<div class="cdx-message__content">' .
                '<p><strong>MySQL/MariaDB查詢失敗。</strong></p>' .
                '<p>請稍後再試。</p>' .
                '</div>' .
                '</div>';
        }

        return $this;
    }

    public function fetchRow() {
        return mysqli_fetch_row($this->result_rows);
    }
}
