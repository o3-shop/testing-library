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

use PHPUnit\Framework\TestCase;

/**
 * This class is used as a base class to run all CE|PE|EE edition tests.
 */
class AllTestsRunner extends TestCase
{

    /** @var array Default test suites */
    protected static $testSuites = array();

    /** @var array Run these tests before any other */
    protected static $priorityTests = array();

    /** @var OxidEsales\TestingLibrary\TestConfig */
    protected static $testConfig;

    /** @var string Filter for test files. */
    protected static $fileFilter = '*Test\.php';

    /** @var array Lower cased test paths. Used to check if test file was not already added. */
    protected static $testFiles = array();

    /**
     * Forms test suite
     *
     * @return \PHPUnit\Framework\TestSuite
     */
    public static function suite()
    {
        $aTestDirectories = static::_getTestDirectories();

        $oSuite = new \PHPUnit\Framework\TestSuite('PHPUnit');

        static::_addPriorityTests($oSuite, static::$priorityTests, $aTestDirectories);

        foreach ($aTestDirectories as $sDirectory) {
            $sFilesSelector = "$sDirectory/" . static::$fileFilter;
            $aTestFiles = glob($sFilesSelector);

            if (empty($aTestFiles)) {
                continue;
            }

            $printer = new \OxidEsales\TestingLibrary\Printer();
            $printer->write( "Adding unit tests from $sFilesSelector\n");

            $aTestFiles = array_diff($aTestFiles, static::$priorityTests);
            $oSuite = static::_addFilesToSuite($oSuite, $aTestFiles);
        }

        return $oSuite;
    }

    /**
     * Adds tests with highest priority.
     *
     * @param TestSuite $oSuite
     * @param array                       $aPriorityTests
     * @param array                       $aTestDirectories
     */
    public static function _addPriorityTests($oSuite, $aPriorityTests, $aTestDirectories)
    {
        if (!empty($aPriorityTests)) {
            $aTestsToInclude = array();
            foreach ($aPriorityTests as $sTestFile) {
                $sFolder = dirname($sTestFile);
                $aDirectories = array_filter($aTestDirectories, function($sTestDirectory) use ($sFolder){
                    return (substr($sTestDirectory, -strlen($sFolder)) === $sFolder);
                });
                if (!empty($aDirectories)) {
                    $fullPath = array_shift($aDirectories) .'/'. basename($sTestFile);
                    if (file_exists($fullPath)) {
                        $aTestsToInclude[] = $fullPath;
                    }
                }
            }
            static::_addFilesToSuite($oSuite, $aTestsToInclude);
        }
    }

    /**
     * Returns array of directories, which should be tested
     *
     * @return array
     */
    protected static function _getTestDirectories()
    {
        $aTestDirectories = array();
        $aTestSuites = getenv('TEST_DIRS')? explode(',', getenv('TEST_DIRS')) : static::$testSuites;

        $testConfig = static::getTestConfig();
        foreach ($aTestSuites as $sSuite) {
            $aTestDirectories[] = $testConfig->getCurrentTestSuite() ."/$sSuite";
        }

        return array_merge($aTestDirectories, static::_getDirectoryTree($aTestDirectories));
    }

    /**
     * Scans given tests directories and returns formed directory tree
     *
     * @param array $aDirectories
     *
     * @return array
     */
    protected static function _getDirectoryTree($aDirectories)
    {
        $aTree = array();

        foreach ($aDirectories as $sDirectory) {
            $aTree = array_merge($aTree, array_diff(glob($sDirectory . "/*", GLOB_ONLYDIR), array('.', '..')));
        }

        if (!empty($aTree)) {
            $aTree = array_merge($aTree, static::_getDirectoryTree($aTree));
        }

        return $aTree;
    }

    /**
     * Adds files to test suite
     *
     * @param TestSuite $oSuite
     * @param array                       $aTestFiles
     *
     * @return TestSuite
     */
    protected static function _addFilesToSuite($oSuite, $aTestFiles)
    {
        foreach ($aTestFiles as $sFilename) {
            $sFilter = getenv('PREG_FILTER');
            if (!$sFilter || preg_match("&$sFilter&i", $sFilename)) {
                $loweredFilename = strtolower($sFilename);
                if (!in_array($loweredFilename, self::$testFiles)) {
                    $oSuite->addTestFile($sFilename);
                    self::$testFiles[] = $loweredFilename;
                }
            }
        }

        return $oSuite;
    }

    /**
     * Returns Test configuration.
     *
     * @return OxidEsales\TestingLibrary\TestConfig
     */
    protected static function getTestConfig()
    {
        if (is_null(static::$testConfig)) {
            static::$testConfig = new OxidEsales\TestingLibrary\TestConfig();
        }

        return static::$testConfig;
    }
}
