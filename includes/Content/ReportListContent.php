<?php

namespace Isais\Content;

use Isais\Content\Content;

class ReportListContent extends ListContent {
    public function __construct($config, $connection_provider, $context, $title)
    {
        parent::__construct($config, $connection_provider, $context, $title);

        $this->display_title = '課程報告';
    }

    public function getListItems()
    {
    }
}
