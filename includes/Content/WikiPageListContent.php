<?php

namespace Isais\Content;

use Isais\Content\ListContent;

class WikiPageListContent extends ListContent {
    public function __construct($config, $connection_provider, $context, $title)
    {
        parent::__construct($config, $connection_provider, $context, $title);

        $this->display_title = '頁面清單';
    }

    public function getListItems()
    {
    }
}
