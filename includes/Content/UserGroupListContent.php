<?php

namespace Isais\Content;

use Isais\Content\ListContent;

class UserGroupListContent extends ListContent {
    public function __construct($config, $connection_provider, $context, $title)
    {
        parent::__construct($config, $connection_provider, $context, $title);

        $this->display_title = '使用者群組';
    }

    public function getListItems()
    {
    }
}
