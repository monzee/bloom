<?php

namespace Codeia\Di;

use Interop\Container\ContainerInterface;

/*
 * This file is a part of the Bloom project.
 * See the LICENSE file at the project root for the terms of use.
 */

/**
 * Description of Component
 *
 * @author Mon Zafra &lt;mz@codeia.ph&gt;
 * @todo provide values
 */
class Component {

    private $services = [];
    private $scopedServices = [];
    private $scope = [];
    private $make;

    function __construct($module, array $services, array $scoped) {
        $this->make = $module;
        $this->services = $services;
        $this->scopedServices = $scoped;
    }

    function make($type, ContainerInterface $context) {
        if (array_key_exists($type, $this->services)) {
            $method = $this->services[$type];
            return $this->make->$method($context);
        } else if (array_key_exists($type, $this->scopedServices)) {
            $method = $this->scopedServices[$type];
            if (!array_key_exists($method, $this->scope)) {
                $this->scope[$method] = $this->make->$method($context);
            }
            return $this->scope[$method];
        } else if ($type === ContainerInterface::class) {
            return $context;
        }
        throw new UnknownServiceError($type);
    }

    function provides($key) {
        return array_key_exists($key, $this->services)
            || array_key_exists($key, $this->scopedServices);
    }

}
