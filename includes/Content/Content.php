<?php

namespace Isais\Content;

abstract class Content {
    protected $auth_manager;

    protected $config;

    protected $connection_provider;

    protected $context;

    protected $has_content = true;

    protected $title;

    protected $head_title = '';

    protected $display_title = '';

    protected $display_subtitle = '';

    protected $display_content = '';

    public function __construct($auth_manager, $config, $connection_provider, $context, $title)
    {
        $this->auth_manager = $auth_manager;
        $this->config = $config;
        $this->connection_provider = $connection_provider;
        $this->context = $context;
        $this->title = $title;
    }

    public function hasContent()
    {
        return $this->has_content;
    }

    public function showNotFoundPage()
    {
        return !$this->has_content;
    }

    public function getHeadTitle()
    {
        if ($this->head_title === '') {
            return $this->display_title;
        }

        return $this->head_title;
    }

    public function getTitle()
    {
        return $this->display_title;
    }

    public function getSubtitle()
    {
        return $this->display_subtitle;
    }

    public function getContent()
    {
        return $this->display_content;
    }
}
