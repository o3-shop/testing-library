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

use OxidEsales\TestingLibrary\TestConfig;
use OxidEsales\TestingLibrary\FileCopier;
use OxidEsales\TestingLibrary\ServiceCaller;
use OxidEsales\TestingLibrary\helpers\ExceptionLogFileHelper;

abstract class BootstrapBase
{
    /** @var TestConfig */
    private $testConfig;

    /** @var int Whether to add demo data when installing the shop. */
    protected $addDemoData = 1;

    /**
     * Initiates class dependencies.
     */
    public function __construct()
    {
        $this->testConfig = new TestConfig();
    }

    /**
     * Prepares tests environment.
     */
    public function init()
    {
        $testConfig = $this->getTestConfig();

        $this->cleanUpExceptionLogFile();
        $this->prepareShop();
        $this->setGlobalConstants();

        if ($testConfig->shouldRestoreShopAfterTestsSuite()) {
            $this->registerResetDbAfterSuite();
        }

        if ($testConfig->shouldInstallShop()) {
            $this->installShop();
        }

        /** @var \OxidEsales\Eshop\Core\Config $config */
        $config = oxNew(\OxidEsales\Eshop\Core\Config::class);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $config);

        /** Reset static variable in oxSuperCfg class, which is base class for every class. */
        $config->setConfig($config);

        $config->init();

        register_shutdown_function(function () {
            $serviceCaller = new ServiceCaller($this->getTestConfig());
            $serviceCaller->setParameter('cleanup', true);
            $serviceCaller->callService('ProjectConfiguration');
        });
    }

    /**
     * Returns tests config.
     *
     * @return TestConfig
     */
    public function getTestConfig()
    {
        return $this->testConfig;
    }

    /**
     * Prepares shop config object.
     */
    protected function prepareShop()
    {
        $testConfig = $this->getTestConfig();

        $shopPath = $testConfig->getShopPath();
        require_once $shopPath .'bootstrap.php';

        $tempDirectory = $testConfig->getTempDirectory();
        if ($tempDirectory && $tempDirectory != '/') {
            $fileCopier = new FileCopier();
            $fileCopier->createEmptyDirectory($tempDirectory);
        }
    }

    /**
     * Sets global constants, as these are still used a lot in tests.
     * This is used to maintain backwards compatibility, but should not be used anymore in new code.
     */
    protected function setGlobalConstants()
    {
        $testConfig = $this->getTestConfig();

        if (!defined('oxPATH')) {
            /** @deprecated use TestConfig::getShopPath() */
            define('oxPATH', $testConfig->getShopPath());
        }

        if (!defined('CURRENT_TEST_SUITE')) {
            /** @deprecated use TestConfig::getCurrentTestSuite() */
            define('CURRENT_TEST_SUITE', $testConfig->getCurrentTestSuite());
        }
    }

    /**
     * Installs the shop.
     *
     * @throws \Exception
     */
    protected function installShop()
    {
        $config = $this->getTestConfig();

        $serviceCaller = new ServiceCaller($this->getTestConfig());
        $serviceCaller->setParameter('addDemoData', $this->addDemoData);
        $serviceCaller->setParameter('turnOnVarnish', $config->shouldEnableVarnish());

        if ($setupPath = $config->getShopSetupPath()) {
            $fileCopier = new FileCopier();
            $remoteDirectory = $config->getRemoteDirectory();
            $shopDirectory = $remoteDirectory ? $remoteDirectory : $config->getShopPath();
            $fileCopier->copyFiles($setupPath, $shopDirectory.'/Setup/');
        }

        try {
            $serviceCaller->callService('ShopInstaller');
        } catch (\Exception $e) {
            exit("Failed to install shop with message: " . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
    }

    /**
     * Creates original database dump and registers database restoration
     * after the tests suite.
     */
    protected function registerResetDbAfterSuite()
    {
        $serviceCaller = new ServiceCaller($this->getTestConfig());
        $serviceCaller->setParameter('dumpDB', true);
        $serviceCaller->setParameter('dump-prefix', 'orig_db_dump');
        try {
            $serviceCaller->callService('ShopPreparation', 1);
        } catch (\Exception $e) {
            define('RESTORE_SHOP_AFTER_TEST_SUITE_ERROR', true);
        }

        register_shutdown_function(function () {
            if (!defined('RESTORE_SHOP_AFTER_TEST_SUITE_ERROR')) {
                $serviceCaller = new ServiceCaller();
                $serviceCaller->setParameter('restoreDB', true);
                $serviceCaller->setParameter('dump-prefix', 'orig_db_dump');
                $serviceCaller->callService('ShopPreparation', 1);
            }
        });
    }

    /**
     * Cleans exception log.
     */
    private function cleanUpExceptionLogFile()
    {
        $exceptionLogHelper = $this->getExceptionLogHelper();
        $exceptionLogHelper->clearExceptionLogFile();
    }

    /**
     * Returns ExceptionLogFileHelper.
     *
     * @return ExceptionLogFileHelper
     */
    private function getExceptionLogHelper()
    {
        $exceptionLogPath = $this->testConfig->getShopPath() . '/log/oxideshop.log';

        return new ExceptionLogFileHelper($exceptionLogPath);
    }
}
