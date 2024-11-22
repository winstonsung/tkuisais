<?php

namespace Isais;

use Isais\Context\Context;

class IsaisServiceContainer
{
    private $services = array();

    private $service_instantiators = array();

    private $services_being_created = array();

    public function loadWiringFile($wiring_file)
    {
        $wiring = require $wiring_file;
        $this->applyWiring($wiring);
    }

    public function loadWiringFiles($wiring_files)
    {
        foreach ($wiring_files as $file) {
            $this->loadWiringFile($file);
        }
    }

    public function applyWiring($service_instantiators)
    {
        foreach ($service_instantiators as $name => $instantiator) {
            $this->defineService($name, $instantiator);
        }
    }

    public function getServiceNames()
    {
        return array_keys($this->service_instantiators);
    }

    public function hasService($name)
    {
        return isset($this->service_instantiators[$name]);
    }

    public function defineService($name, $instantiator)
    {
        if ($this->hasService($name)) {
            exit("ServiceAlreadyDefinedException $name");
        }

        $this->service_instantiators[$name] = $instantiator;
    }

    public function redefineService($name, $instantiator)
    {
        if (!$this->hasService($name)) {
            exit("NoSuchServiceException $name");
        }

        if (isset($this->services[$name])) {
            exit("CannotReplaceActiveServiceException $name");
        }

        $this->service_instantiators[$name] = $instantiator;
    }

    private function createService($name)
    {
        if (!$this->hasService($name)) {
            exit("NoSuchServiceException $name");
        }

        if (isset($this->services_being_created[$name])) {
            exit("RecursiveServiceDependencyException " .
                "Circular dependency when creating service! " .
                implode(' -> ', array_keys($this->services_being_created)) . " -> $name");
        }

        $this->services_being_created[$name] = true;

        $service = $this->service_instantiators[$name](
            $this
        );

        return $service;
    }

    public function getService($name)
    {
        if (!isset($this->services[$name])) {
            $this->services[$name] = $this->createService($name);
        }

        return $this->services[$name];
    }

    /** Service helper functions */

    public function getAuthManager()
    {
        return $this->getService('AuthManager');
    }

    public function getConfig()
    {
        return $this->getService('Config');
    }

    public function getConnectionProvider()
    {
        return $this->getService('ConnectionProvider');
    }

    public function getContext()
    {
        return $this->getService('Context');
    }

    public function getEntryPoint()
    {
        return $this->getService('EntryPoint');
    }

    public function getLanguageData()
    {
        return $this->getService('LanguageData');
    }

    public function getLanguageTag()
    {
        return $this->getService('LanguageTag');
    }

    public function getLanguageTagFactory()
    {
        return $this->getService('LanguageTagFactory');
    }

    public function getResourceLoader()
    {
        return $this->getService('ResourceLoader');
    }

    public function getSkin()
    {
        return $this->getService('Skin');
    }

    public function getTitle()
    {
        return $this->getService('Title');
    }

    public function getUser()
    {
        return $this->getService('User');
    }
}
