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
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\SkippedTestError;
use PHPUnit\Framework\TestCase;

/**
 * Base tests class. Most tests should extend this class.
 */
abstract class BaseTestCase extends TestCase
{

    /** @var TestConfig */
    private static $testConfig;

    protected TestHandler $testLogHandler;


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
        $this->testLogHandler = new TestHandler();
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
        $this->testLogHandler->clear();
        $logger = new Logger('test', [$this->testLogHandler]);
        \OxidEsales\Eshop\Core\Registry::set('logger', $logger);

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Asserts that exactly one exception was logged at ERROR level, that it is an instance of
     * the given class, and optionally that its message contains the given string.
     * Clears the handler after the assertion.
     *
     * @param string $expectedExceptionClass  Fully-qualified class name of the expected exception.
     * @param string $expectedExceptionMessage Optional substring expected in the exception message.
     */
    protected function assertLoggedException(string $expectedExceptionClass, string $expectedExceptionMessage = ''): void
    {
        $errorRecords = array_filter(
            $this->testLogHandler->getRecords(),
            static function (array $record): bool {
                return $record['level'] >= Logger::ERROR;
            }
        );

        $this->assertCount(1, $errorRecords, 'Expected exactly one ERROR-level log record.');

        $record = reset($errorRecords);

        $exception = null;
        foreach ($record['context'] as $contextValue) {
            if ($contextValue instanceof \Throwable) {
                $exception = $contextValue;
                break;
            }
        }

        $this->assertNotNull($exception, 'No Throwable found in the log record context.');
        $this->assertInstanceOf($expectedExceptionClass, $exception);

        if ($expectedExceptionMessage !== '') {
            $this->assertStringContainsString($expectedExceptionMessage, $exception->getMessage());
        }

        $this->testLogHandler->clear();
    }

    /**
     * Fails the test if any ERROR-level (or higher) records are present in the log handler.
     * Clears the handler after the check.
     */
    protected function failOnLoggedExceptions(): void
    {
        $errorRecords = array_filter(
            $this->testLogHandler->getRecords(),
            static function (array $record): bool {
                return $record['level'] >= Logger::ERROR;
            }
        );

        if (!empty($errorRecords)) {
            $messages = array_map(
                static function (array $record): string {
                    return $record['message'];
                },
                $errorRecords
            );

            $this->testLogHandler->clear();
            $this->fail('Test failed with logged exception(s): ' . implode('; ', $messages));
        }

        $this->testLogHandler->clear();
    }

    /**
     * Clears all records from the test log handler without triggering a failure.
     * Use this when a logged exception is expected and already verified separately.
     */
    protected function clearExpectedLoggedExceptions(): void
    {
        $this->testLogHandler->clear();
    }
}