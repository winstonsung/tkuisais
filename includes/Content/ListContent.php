<?php

namespace Isais\Content;

use Isais\Content\Content;

abstract class ListContent extends Content {
    protected $list_items;

    public function getListItems() {
        return $this->$list_items;
    }
}
