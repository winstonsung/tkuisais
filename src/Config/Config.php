<?php

namespace Isais\Config;

class Config
{
    private $options = array();

    public function __construct(
        $default_config_options,
        $local_config_options
    ) {
        foreach ($default_config_options as $name => $value) {
            $this->addOption($name, $value);
        }

        foreach ($local_config_options as $name => $value) {
            $this->setOption($name, $value);
        }
    }

    public function hasOption($name)
    {
        return (
            isset($this->options[$name]) &&
            isset($this->options[$name]['default'])
        );
    }

    public function getDefaultOption($name)
    {
        if (!$this->hasOption($name)) {
            die("Undefined config option $name");
        }

        return $this->options[$name]['default'];
    }

    public function getOption($name)
    {
        if (isset($this->options[$name])) {
            if (isset($this->options[$name]['default'])) {
                return $this->options[$name]['default'];
            } elseif (isset($this->options[$name]['value'])) {
                return $this->options[$name]['value'];
            }
        }

        return null;
    }

    public function addOption($name, $value)
    {
        if ($this->hasOption($name)) {
            die("Tried to redefine config option $name");
        }

        $this->options[$name]['default'] = $value;
    }

    public function setOption($name, $value)
    {
        if ($this->hasOption($name)) {
            $this->options[$name]['value'] = $value;
        }
    }
}
