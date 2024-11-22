<?php

namespace Isais\Content;

use Isais\Content\Content;

class UnitListContent extends ListContent {
    public function __construct($config, $connection_provider, $context, $title)
    {
        parent::__construct($config, $connection_provider, $context, $title);

        $this->display_title = '單位';
    }

    public function getListItems()
    {
    }
}
