<?php

namespace Isais\Content;

use Isais\Content\Content;

class ReportContent extends Content {
    public function __construct($auth_manager, $config, $connection_provider, $context, $title)
    {
        parent::__construct($auth_manager, $config, $connection_provider, $context, $title);

        $item = $this->title->getItem();

        if ($item === 'mid-term') {
            $this->display_title = '期中報告';
            $this->display_content = '<p><a href="' .
                'http://163.13.175.8/113dbb/113dbb04/tkufd/docs/資料庫概論期中書面報告_06.pdf' .
                '">PDF檔</a></p>' .
                '<object type="application/pdf" data="' .
                'http://163.13.175.8/113dbb/113dbb04/tkufd/docs/資料庫概論期中書面報告_06.pdf' .
                '"></object>';
        } elseif ($item === 'final_term') {
            $this->display_title = '期末報告';
            $this->display_content = '<p><a href="' .
                'http://163.13.175.8/113dbb/113dbb04/tkufd/docs/資料庫概論期中書面報告_06.pdf' .
                '">PDF檔</a></p>' .
                '<object type="application/pdf" data="' .
                'http://163.13.175.8/113dbb/113dbb04/tkufd/docs/資料庫概論期中書面報告_06.pdf' .
                '"></object>';
        } else {
            $this->has_content = false;
        }
    }
}
