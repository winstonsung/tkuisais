<?php

namespace Isais\Language;

use Isais\Config\Config;
use Isais\Language\LanguageTag;

class LanguageTagFactory
{
    private $config = null;

    private $languages = array();

    public function __construct(
        $config = null
    ) {
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
        if (
            isset($this->config) &&
            $this->config->getOption()
        ) {
        } else {
        }

        return $this->getLanguageTag($tag);
    }
}
