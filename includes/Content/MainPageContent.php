<?php

namespace Isais\Content;

use Isais\Content\Content;

class MainPageContent extends Content {
    public function __construct($config, $connection_provider, $context, $title)
    {
        parent::__construct($config, $connection_provider, $context, $title);

        $this->display_title = '歡迎使用iSAIS校務資訊系統！';
        $this->display_content = '<h2>最新公告</h2>' .
            '<h2>近期活動</h2>' .
            '<h2>課程</h2>' .
            '<h2>社團</h2>' .
            '<h2>近期活動</h2>' .
            '<h2>銀行清單</h2>' .
            '<h2>課程報告</h2>';
    }
}
