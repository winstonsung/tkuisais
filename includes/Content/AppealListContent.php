<?php

namespace Isais\Content;

use Isais\Content\ListContent;

class AppealListContent extends ListContent {
    public function __construct($config, $connection_provider, $context, $title)
    {
        parent::__construct($config, $connection_provider, $context, $title);

        $this->display_title = '申訴';
    }

    public function getListItems()
    {
    }
}
