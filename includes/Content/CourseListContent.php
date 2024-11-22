<?php

namespace Isais\Content;

use Isais\Content\Content;

class CourseContent extends Content {
    public function __construct($config, $connection_provider, $context, $title)
    {
        parent::__construct($config, $connection_provider, $context, $title);

        $this->display_title = '課程';
    }

    public function getListItems()
    {
    }
}
