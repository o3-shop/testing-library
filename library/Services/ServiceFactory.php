<?php
/**
 * This file is part of O3-Shop Testing library.
 *
 * O3-Shop is free software: you can redistribute it and/or modify  
 * it under the terms of the GNU General Public License as published by  
 * the Free Software Foundation, version 3.
 *
 * O3-Shop is distributed in the hope that it will be useful, but 
 * WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU 
 * General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with O3-Shop.  If not, see <http://www.gnu.org/licenses/>
 *
 * @copyright  Copyright (c) 2022 OXID eSales AG (https://www.oxid-esales.com)
 * @copyright  Copyright (c) 2022 O3-Shop (https://www.o3-shop.com)
 * @license    https://www.gnu.org/licenses/gpl-3.0  GNU General Public License 3 (GPLv3)
 */
namespace OxidEsales\TestingLibrary\Services;

use Exception;
use OxidEsales\TestingLibrary\Services\Library\ServiceConfig;
use OxidEsales\TestingLibrary\Services\Library\ShopServiceInterface;

/**
 * Services Factory class.
 */
class ServiceFactory
{
    /**
     * Loads the shop.
     *
     * @param ServiceConfig $config
     */
    public function __construct($config)
    {
        $this->config = $config;

        include_once $config->getShopDirectory() . '/bootstrap.php';
    }

    /**
     * Creates Service object. All services must implement ShopService interface
     *
     * @param string $serviceClass
     *
     * @throws Exception
     *
     * @return ShopServiceInterface
     */
    public function createService($serviceClass)
    {
        $className = $serviceClass;
        if (!$this->isNamespacedClass($serviceClass)) {
            // Used for backwards compatibility.
            $className = $this->formClassName($serviceClass);
        }
        if (!class_exists($className)) {
            throw new Exception("Service '$serviceClass' was not found!");
        }
        $service = new $className($this->getServiceConfig());

        if (!($service instanceof ShopServiceInterface)) {
            throw new Exception("Service '$className' does not implement ShopServiceInterface interface!");
        }

        return $service;
    }

    /**
     * Includes service main class file
     *
     * @param string $serviceClass
     *
     * @return string
     */
    protected function formClassName($serviceClass)
    {
        return "OxidEsales\\TestingLibrary\\Services\\$serviceClass\\$serviceClass";
    }

    /**
     * @return ServiceConfig
     */
    protected function getServiceConfig()
    {
        return $this->config;
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    private function isNamespacedClass($className)
    {
        return strpos($className, '\\') !== false;
    }
}
