<?php

namespace Isais\Language;

class LanguageData {
    private $deprecated_language_mapping = array();

    private $bcp_47_languages_normalized = array();

    private $all_languages = array();

    public function __construct()
    {
    }

    public function getDeprecatedLanguageMapping() {
        return $this->deprecated_language_mapping;
    }

    public function getNormalizedBcp47Languages() {
        return $this->bcp_47_languages_normalized;
    }
}
