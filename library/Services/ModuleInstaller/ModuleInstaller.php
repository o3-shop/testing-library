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
namespace OxidEsales\TestingLibrary\Services\ModuleInstaller;


use Exception;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\TestingLibrary\Services\Library\Request;
use OxidEsales\TestingLibrary\Services\Library\ServiceConfig;
use OxidEsales\TestingLibrary\Services\Library\ShopServiceInterface;

/**
 * Class for module installation.
 */
class ModuleInstaller implements ShopServiceInterface
{
    /**
     * @param ServiceConfig $config
     */
    public function __construct($config) {}

    /**
     * Starts installation of the shop.
     *
     * @param Request $request
     *
     */
    public function init($request)
    {
        if (($shopId = $request->getParameter('shp')) && (1 < $shopId)) {
            $this->switchToShop($shopId);
        }

        $modulesToActivate = $request->getParameter("modulestoactivate");
        $moduleDirectory = \OxidEsales\Eshop\Core\Registry::getConfig()->getModulesDir();

        $this->prepareModulesForActivation($moduleDirectory);
        foreach ($modulesToActivate as $modulePath) {
            $this->installModule($modulePath);
        }

        $this->makeModuleServicesAvailableInDIContainer();
    }

    /**
     * Switch to subshop.
     * 
     * @param integer $shopId
     *
     * @return integer
     */
    public function switchToShop($shopId)
    {
        $_POST['shp'] = $shopId;
        $_POST['actshop'] = $shopId;
        $keepThese = [\OxidEsales\Eshop\Core\ConfigFile::class];
        $registryKeys = Registry::getKeys();
        foreach ($registryKeys as $key) {
            if (in_array($key, $keepThese)) {
                continue;
            }
            Registry::set($key, null);
        }
        $utilsObject = new \OxidEsales\Eshop\Core\UtilsObject;
        $utilsObject->resetInstanceCache();
        Registry::set(\OxidEsales\Eshop\Core\UtilsObject::class, $utilsObject);
        \OxidEsales\Eshop\Core\Module\ModuleVariablesLocator::resetModuleVariables();
        Registry::getSession()->setVariable('shp', $shopId);
        Registry::set(\OxidEsales\Eshop\Core\Config::class, null);
        Registry::getConfig()->setConfig(null);
        Registry::set(\OxidEsales\Eshop\Core\Config::class, null);
        $moduleVariablesCache = new \OxidEsales\Eshop\Core\FileCache();
        $shopIdCalculator = new \OxidEsales\Eshop\Core\ShopIdCalculator($moduleVariablesCache);
        return  $shopIdCalculator->getShopId();
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

        $moduleCache = oxNew(\OxidEsales\Eshop\Core\Module\ModuleCache::class, $module);
        $moduleInstaller = oxNew(\OxidEsales\Eshop\Core\Module\ModuleInstaller::class, $moduleCache);
        if (!$moduleInstaller->activate($module)) {
            throw new Exception("Error on module installation: " . $module->getId());
        }
    }

    /**
     * Prepares modules for activation. Registers all modules that exist in the shop.
     *
     * @param string $moduleDirectory The base directory of modules.
     */
    private function prepareModulesForActivation($moduleDirectory)
    {
        $moduleList = oxNew(\OxidEsales\Eshop\Core\Module\ModuleList::class);
        $moduleList->getModulesFromDir($moduleDirectory);
    }

    /**
     * Loads module object from given directory.
     *
     * @param string $modulePath The path to the module.
     *
     * @return \OxidEsales\Eshop\Core\Module\Module
     * @throws Exception
     */
    private function loadModule($modulePath)
    {
        $module = oxNew(\OxidEsales\Eshop\Core\Module\Module::class);
        if (!$module->loadByDir($modulePath)) {
            throw new Exception("Module not found");
        }
        return $module;
    }

    private function makeModuleServicesAvailableInDIContainer(): void
    {
        ContainerFactory::resetContainer();
    }
}
