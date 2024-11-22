<?php

namespace Isais\Content;

use Isais\Content\Content;

class CourseContent extends Content {
    public function __construct($auth_manager, $config, $connection_provider, $context, $title)
    {
        parent::__construct($auth_manager, $config, $connection_provider, $context, $title);

        $this->display_title = '課程';
    }

    public function getListItems()
    {
    }
}
