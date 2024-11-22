<?php

namespace Isais\Language;

class LanguageTag {
    private $bcp_47_language_tag;

    public function __construct(
        $tag
    ) {
        $this->bcp_47_language_tag = $tag;
    }

    // Get internal BCP 47 tag
    public function getTag() {
        return $this->bcp_47_language_tag;
    }

    public function getOutputBcp47Tag() {
        return $this->bcp_47_language_tag;
    }
}
