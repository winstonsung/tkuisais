<?php

namespace Isais\Language;

use Isais\Data\LanguageData;
use Isais\Language\LanguageTag;

class LanguageTagFactory
{
    private $config;

    private $languages;

    public function __construct(
        $language_data,
        $config
    ) {
        $this->languages = $language_data->getAllLanguages();
        $this->config = $config;
    }

    // Get LanguageTag object by internal BCP 47 tag
    public function getLanguageTag($tag) {
        if (!isset($this->languages[$tag])) {
            $this->languages[$tag] = new LanguageTag($tag);
        }

        return $this->languages[$tag];
    }

    public function getLanguageTagByExternalBcp47Tag($tag) {
        // Config remapping

        return $this->getLanguageTag($tag);
    }
}
