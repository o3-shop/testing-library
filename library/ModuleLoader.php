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

namespace OxidEsales\TestingLibrary;

use OxidEsales\Eshop\Core\Module\ModuleList;
use OxidEsales\Eshop\Core\Module\ModuleCache;
use OxidEsales\Eshop\Core\Module\ModuleInstaller;
use OxidEsales\Eshop\Core\Module\Module;
use Exception;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\TestingLibrary\Services\Library\Cache;

/**
 * Module loader class. Can imitate loaded module for testing.
 */
class ModuleLoader
{
    /**
     * @var bool Whether to use original chains.
     */
    protected static $useOriginalChains = false;

    /**
     * Sets the original chain loading command
     *
     * @param boolean $original
     */
    public function useOriginalChain($original)
    {
        self::$useOriginalChains = $original;
    }

    /**
     * Loads modules and activates them.
     *
     * @param array $modulesToActivate Array of modules to load.
     */
    public function activateModules($modulesToActivate)
    {
        $this->clearModuleChain();

        // First load all needed config options before the module will be installed.
        $this->prepareModulesForActivation();
        foreach ($modulesToActivate as $modulePath) {
            $this->installModule($modulePath);
        }

        $this->makeModuleServicesAvailableInDIContainer();

        // Reset reverse proxy backend as module activation sets it to flush mode.
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Cache\ReverseProxy\ReverseProxyBackend::class, null);
    }

    /**
     * Prepares modules for activation. Registers all modules that exist in the shop.
     */
    private function prepareModulesForActivation()
    {
        $moduleDirectory = \OxidEsales\Eshop\Core\Registry::getConfig()->getModulesDir();
        $moduleList = new ModuleList();
        $moduleList->getModulesFromDir($moduleDirectory);
    }

    /**
     * Activates module.
     *
     * @param string $modulePath The path to the module.
     *
     * @throws Exception
     */
    public function installModule($modulePath)
    {
        $module = $this->loadModule($modulePath);
        if ($module->isActive()) {
            return;
        }

        $moduleCache = new ModuleCache($module);
        $moduleInstaller = new ModuleInstaller($moduleCache);

        /** Clean all caches before module activation */
        $this->clearShopTmpFolder();

        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getInstance();
        $database->flushTableDescriptionCache();

        $cachedClassInstances = Registry::getKeys();

        if (!$moduleInstaller->activate($module)) {
            throw new Exception("Error on module installation: " . $module->getId());
        }

        foreach ($cachedClassInstances as $cachedClassInstance) {
            if (\OxidEsales\Eshop\Core\ConfigFile::class !== $cachedClassInstance) {
                Registry::set($cachedClassInstance, null);
            }
        }
        $baseClass = new \OxidEsales\Eshop\Core\Base();
        $baseClass->setConfig(null);
        $baseClass->setSession(null);
        $baseClass->setUser(null);
        $baseClass->setAdminMode(null);

        if (method_exists($baseClass, 'setRights')) {
            $baseClass->setRights(null);
        }

        $this->clearShopTmpFolder();
    }

    /**
     * Loads module object from given directory.
     *
     * @param string $modulePath The path to the module.
     *
     * @return Module
     * @throws Exception
     */
    private function loadModule($modulePath)
    {
        $module = new Module();
        if (!$module->loadByDir($modulePath)) {
            throw new Exception('Module configuration not found for module with path ' . $modulePath);
        }
        return $module;
    }

    /**
     * Checks if extended files have to be added to "original" module chain or to empty chain.
     */
    private function clearModuleChain()
    {
        if (!self::$useOriginalChains) {
            \OxidEsales\Eshop\Core\Registry::getConfig()->setConfigParam("aModules", '');
        }
    }

    /**
     * Shop cache should be deleted as some modules might try to clean cache on top.
     * This creates problems if CI and Apache user is different.
     * Some modules might not clean cache which would also lead to a random errors/failures.
     */
    private function clearShopTmpFolder()
    {
        $cache = new Cache();
        $cache->clearTemporaryDirectory();
    }

    private function makeModuleServicesAvailableInDIContainer(): void
    {
        ContainerFactory::resetContainer();
    }
}
