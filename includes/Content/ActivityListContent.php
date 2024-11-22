<?php

namespace Isais\Content;

use Isais\Content\ListContent;

class ActivityListContent extends ListContent {
    public function __construct($auth_manager, $config, $connection_provider, $context, $title)
    {
        parent::__construct($auth_manager, $config, $connection_provider, $context, $title);

        $this->display_title = '活動';
    }

    public function getListItems()
    {
    }
}
