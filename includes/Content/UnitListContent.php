<?php

namespace Isais\Content;

use Isais\Content\Content;

class UnitListContent extends ListContent {
    public function __construct($auth_manager, $config, $connection_provider, $context, $title)
    {
        parent::__construct($auth_manager, $config, $connection_provider, $context, $title);

        if ($this->title->getItem() === 'new') {
            $this->display_title = '新增單位';
        }

        $this->display_title = '單位';
    }

    public function getListItems()
    {
    }
}
