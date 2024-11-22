<?php

namespace Isais;

class IsaisServiceContainer
{
    private $services = array();

    private $service_instantiators = array();

    private $services_being_created = array();

    public function loadWiringFiles($wiring_files)
    {
        foreach ($wiring_files as $file) {
            $wiring = require $file;
            $this->applyWiring($wiring);
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
        return isset($this->serviceInstantiators[$name]);
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
        unset($this->disabled[$name]);
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

        $service = ($this->service_instantiators[$name])(
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

    public function getConfig() {
        return $this->getService('Config');
    }

    public function getContext() {
        return $this->getService('getContext');
    }

    public function getEntryPoint() {
        return $this->getService('EntryPoint');
    }
}
