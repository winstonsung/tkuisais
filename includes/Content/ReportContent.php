<?php

namespace Isais\Content;

use Isais\Content\Content;

class ReportContent extends Content {
    public function getHtml() {
        return '<object type="application/pdf" data="' .
            'http://163.13.175.8/113dbb/113dbb04/tkufd/docs/資料庫概論期中書面報告_06.pdf' .
            '"></object>';
    }
}
