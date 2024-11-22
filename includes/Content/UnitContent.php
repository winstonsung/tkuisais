<?php

namespace Isais\Content;

use Isais\Content\Content;

class UnitContent extends Content {
    public function __construct($config, $connection_provider, $context, $title)
    {
        parent::__construct($config, $connection_provider, $context, $title);

        $unit_name = $this->title->getUnitName();

        $this->display_title = $unit_name;
        $this->display_content = '<h2>最新公告</h2>' .
            '<h2>近期活動</h2>';
    }
}
