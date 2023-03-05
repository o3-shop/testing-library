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

use DateTime;
use PHPUnit\Framework\SkippedTestError;
use PHPUnit\Framework\TestCase;

/**
 * Base tests class. Most tests should extend this class.
 */
abstract class BaseTestCase extends TestCase
{

    /** @var TestConfig */
    private static $testConfig;

    protected $exceptionLogHelper;


    /**
     * BaseTestCase constructor.
     *
     * @param null   $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->exceptionLogHelper = new \OxidEsales\TestingLibrary\helpers\ExceptionLogFileHelper(OX_LOG_FILE);
    }

    /**
     * Returns test configuration.
     *
     * @return TestConfig
     */
    public static function getStaticTestConfig()
    {
        if (is_null(self::$testConfig)) {
            self::$testConfig = new TestConfig();
        }

        return self::$testConfig;
    }

    /**
     * Returns test configuration.
     *
     * @return TestConfig
     */
    public function getTestConfig()
    {
        return self::getStaticTestConfig();
    }

    /**
     * Mark the test as skipped until given date.
     * Wrapper function for PHPUnit\Framework\Assert::markTestSkipped.
     *
     * @param string $sDate    Date string in format 'Y-m-d'.
     * @param string $sMessage Message.
     *
     * @throws SkippedTestError
     */
    public function markTestSkippedUntil($sDate, $sMessage = '')
    {
        $oDate = DateTime::createFromFormat('Y-m-d', $sDate);

        if (time() < ((int) $oDate->format('U'))) {
            $this->markTestSkipped($sMessage);
        }
    }

    /**
     * Activates the theme for running acceptance tests on.
     *
     * @todo Refactor this method to use ThemeSwitcher service. This will require a prior refactoring of the testing library.
     *
     * @param string $themeName Name of the theme to activate
     *
     * @throws \OxidEsales\Eshop\Core\Exception\SystemComponentException
     */
    public function activateTheme($themeName)
    {
        $currentShopId = \OxidEsales\Eshop\Core\Registry::getConfig()->getShopId();

        $theme = oxNew(\OxidEsales\Eshop\Core\Theme::class);
        $theme->load($themeName);

        $testConfig = new TestConfig();
        $shopId = $testConfig->getShopId();
        \OxidEsales\Eshop\Core\Registry::getConfig()->setShopId($shopId);

        $theme->activate();

        /**
         * In the tests, the main shops' theme always hay to be switched too.
         * If the current shop is not a parent shop (i.e. shopId == 1), activate the theme in the parent shop as well.
         */
        if ($shopId != 1) {
            \OxidEsales\Eshop\Core\Registry::getConfig()->setShopId(1);

            $theme->activate();
        }

        \OxidEsales\Eshop\Core\Registry::getConfig()->setShopId($currentShopId);
    }

    /**
     * @throws \OxidEsales\Eshop\Core\Exception\StandardException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->failOnLoggedExceptions();
    }

    /**
     * @throws \OxidEsales\Eshop\Core\Exception\StandardException
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->failOnLoggedExceptions();
    }

    /**
     * @param string $expectedExceptionClass
     * @param string $expectedExceptionMessage
     *
     * @throws \OxidEsales\Eshop\Core\Exception\StandardException
     */
    protected function assertLoggedException($expectedExceptionClass, $expectedExceptionMessage = '')
    {
        $this->assertCount(
            1,
            $this->exceptionLogHelper->getParsedExceptions()
        );

        $this->assertStringContainsString(
            $expectedExceptionClass,
            $this->exceptionLogHelper->getParsedExceptions()[0]
        );

        if ($expectedExceptionMessage) {
            $this->assertStringContainsString(
                $expectedExceptionMessage,
                $this->exceptionLogHelper->getParsedExceptions()[0]
            );
        }

        $this->exceptionLogHelper->clearExceptionLogFile();
    }

    /**
     * @throws \OxidEsales\Eshop\Core\Exception\StandardException
     */
    protected function failOnLoggedExceptions()
    {
        if ($exceptionLogEntries = $this->exceptionLogHelper->getExceptionLogFileContent()) {
            $this->exceptionLogHelper->clearExceptionLogFile();
            $this->fail('Test failed with ' . OX_LOG_FILE . ' entry:' . $exceptionLogEntries);
        }
    }
}