<?php

namespace Isais\Content;

use Isais\Content\ListContent;

class AnnouncementListContent extends ListContent {
    public function __construct($config, $connection_provider, $context, $title)
    {
        parent::__construct($config, $connection_provider, $context, $title);

        $this->display_title = '公告';
    }

    public function getListItems()
    {
    }
}
