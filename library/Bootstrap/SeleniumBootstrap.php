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

namespace OxidEsales\TestingLibrary\Bootstrap;

use OxidEsales\TestingLibrary\AcceptanceTestCase;
use OxidEsales\TestingLibrary\FileCopier;

class SeleniumBootstrap extends BootstrapBase
{
    /** @var int Whether to add demo data when installing the shop. */
    protected $addDemoData = 1;

    /**
     * Initiates shop before testing.
     */
    public function init()
    {
        parent::init();

        define("SHOP_EDITION", 'PE_CE');

        $this->prepareScreenShots();
        $this->copyTestFilesToShop();

        /** @var \OxidEsales\Eshop\Core\Config $config */
        $config = oxNew(\OxidEsales\Eshop\Core\Config::class);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $config);

        /** Reset static variable in \OxidEsales\Eshop\Core\Base class, which is base class for every class. */
        $config->setConfig($config);

        register_shutdown_function(function () {
            AcceptanceTestCase::stopMinkSession();
        });
    }

    /**
     * Creates screenshots directory if it does not exists.
     */
    public function prepareScreenShots()
    {
        $screenShotsPath = $this->getTestConfig()->getScreenShotsPath();
        if ($screenShotsPath && !is_dir($screenShotsPath)) {
            mkdir($screenShotsPath, 0777, true);
        }
    }

    /**
     * Sets global constants, as these are still used a lot in tests.
     * This is used to maintain backwards compatibility, but should not be used anymore in new code.
     */
    protected function setGlobalConstants()
    {
        parent::setGlobalConstants();
        $testConfig = $this->getTestConfig();

        /** @deprecated use TestConfig::getShopUrl() */
        define('shopURL', $testConfig->getShopUrl());

        /** @deprecated use TestConfig::getShopId() */
        define('oxSHOPID', $testConfig->getShopId());

        /** @deprecated use TestConfig::isSubShop() */
        define('isSUBSHOP', $testConfig->isSubShop());
    }

    /**
     * Some test files are needed to successfully run selenium tests.
     * Currently only files needed for clearing cookies are copied.
     */
    public function copyTestFilesToShop()
    {
        $config = $this->getTestConfig();
        $target = $config->getRemoteDirectory() ? $config->getRemoteDirectory().'/_cc.php' : $config->getShopPath().'/_cc.php';
        $fileCopier = new FileCopier();
        $fileCopier->copyFiles(TEST_LIBRARY_PATH .'_cc.php', $target, true);
    }
}
