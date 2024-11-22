<?php

namespace Isais\Data;

class LanguageData {
    private $config;

    private $all_languages = array(
        'cdo-hant' => '閩東語 - 傳統漢字',
        'cdo-latn' => 'Mìng-dĕ̤ng-ngṳ̄ - Bàng-uâ-cê',
        'en' => 'English',
        'hak-hant' => '客家語 - 繁體',
        'hak-latn' => 'Hak-kâ-ngî - Pha̍k-fa-sṳ',
        'nan-hant' => '閩南語 - 傳統漢字',
        'nan-latn-pehoeji' => 'Bân-lâm-gú - Pe̍h-ōe-jī',
        'nan-latn-tailo' => 'Bân-lâm-gú - Tâi-lô',
        'yue-hant' => '粵語 - 繁體',
        'zh-hant' => '中文 - 臺灣正體',
        'zh-hant-hk' => '中文 - 香港繁體',
    );

    public function __construct(
        $config
    ) {
        $this->config = $config;
    }

    public function getAllLanguages() {
        return $this->all_languages;
    }
}
