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

namespace OxidEsales\TestingLibrary\Tests\Integration\helpers;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ExceptionLogFileHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderWrongConstructorParameters
     * @covers       \OxidEsales\TestingLibrary\helpers\ExceptionLogFileHelper::__construct
     *
     * @param $constructorParameters
     */
    public function testConstructorThrowsExpectedExceptionOnWrongParameters($constructorParameters)
    {
        $this->expectException(
            \OxidEsales\Eshop\Core\Exception\StandardException::class
        );
        $this->expectExceptionMessage('Constructor parameter $exceptionLogFile must be a non empty string');
        new \OxidEsales\TestingLibrary\helpers\ExceptionLogFileHelper($constructorParameters);
    }

    public function dataProviderWrongConstructorParameters()
    {
        return [
            [''],
            [[]],
            [new \StdClass()],
            [false],
            [true],
            [1],
            [0],
        ];
    }

    /**
     * @dataProvider dataProviderExpectedContent
     * @covers       \OxidEsales\TestingLibrary\helpers\ExceptionLogFileHelper::getExceptionLogFileContent
     *
     * @throws \OxidEsales\Eshop\Core\Exception\StandardException
     */
    public function testGetExceptionLogFileContentReturnsExpectedContent($expectedContent)
    {
        $exceptionLogFileResource = tmpfile();
        $exceptionLogFile = stream_get_meta_data($exceptionLogFileResource)['uri'];
        fwrite($exceptionLogFileResource, $expectedContent);

        $exceptionLogFileHelper = new \OxidEsales\TestingLibrary\helpers\ExceptionLogFileHelper($exceptionLogFile);

        $actualContent = $exceptionLogFileHelper->getExceptionLogFileContent();

        fclose($exceptionLogFileResource);

        $this->assertSame($expectedContent, $actualContent);
    }

    public function dataProviderExpectedContent()
    {
        return [
            [''],
            ['test'],
            ['tèßt'],
            ["
            
            test
            
            "]
        ];
    }

    /**
     * @covers \OxidEsales\TestingLibrary\helpers\ExceptionLogFileHelper::clearExceptionLogFile
     */
    public function testClearExceptionLogFileThrowsExceptionOnFileNotWritable()
    {
        $exceptionLogFileRessource = tmpfile();
        fwrite($exceptionLogFileRessource, 'test');
        $exceptionLogFile = stream_get_meta_data($exceptionLogFileRessource)['uri'];

        $expectedExceptionMessage = 'File ' . $exceptionLogFile . ' could not be opened in write mode';

        $exceptionLogFileHelper = new \OxidEsales\TestingLibrary\helpers\ExceptionLogFileHelper($exceptionLogFile);
        chmod($exceptionLogFile, 0444);
        $this->assertFalse(is_writable($exceptionLogFile));

        $actualExceptionMessage = '';
        $exceptionThrown = false;
        try {
            // We do not want the E_WARNING issued by file_get_contents to break our test
            $originalErrorReportingLevel = error_reporting();
            error_reporting($originalErrorReportingLevel & ~E_WARNING);
            $exceptionLogFileHelper->clearExceptionLogFile();
        } catch (\OxidEsales\Eshop\Core\Exception\StandardException $actualException) {
            $actualExceptionMessage = $actualException->getMessage();
            $exceptionThrown = true;
        } finally {
            error_reporting($originalErrorReportingLevel);
            fclose($exceptionLogFileRessource);
        }

        $this->assertEquals($expectedExceptionMessage, $actualExceptionMessage);
        $this->assertTrue($exceptionThrown);
    }

    /**
     * @covers \OxidEsales\TestingLibrary\helpers\ExceptionLogFileHelper::clearExceptionLogFile
     *
     * @throws \OxidEsales\Eshop\Core\Exception\StandardException
     */
    public function testClearExceptionLogFileDeletesExceptionLogFileContent()
    {
        $exceptionLogFileRessource = tmpfile();
        fwrite($exceptionLogFileRessource, 'test');
        $exceptionLogFile = stream_get_meta_data($exceptionLogFileRessource)['uri'];

        $exceptionLogFileHelper = new \OxidEsales\TestingLibrary\helpers\ExceptionLogFileHelper($exceptionLogFile);
        $exceptionLogFileHelper->clearExceptionLogFile();

        $actualContent = $exceptionLogFileHelper->getExceptionLogFileContent();

        fclose($exceptionLogFileRessource);

        $this->assertEmpty($actualContent);
    }

    /**
     * @dataProvider dataProviderNumberOfExceptionsToBeLogged
     * @covers       \OxidEsales\TestingLibrary\helpers\ExceptionLogFileHelper::getParsedExceptions
     *
     * @throws \OxidEsales\Eshop\Core\Exception\StandardException
     */
    public function testGetParsedExceptionsReturnsExpectedValue($exceptionsToBeLogged)
    {
        $expectedLevel = 'ERROR';
        $expectedType = \Exception::class;
        $expectedMessage = 'test message';

        $exception = new \Exception($expectedMessage);

        $exceptionLogFileRessource = tmpfile();
        $exceptionLogFile = stream_get_meta_data($exceptionLogFileRessource)['uri'];

        $lineFormatter = new LineFormatter();
        $lineFormatter->includeStacktraces(true);

        $streamHandler = new StreamHandler(
            $exceptionLogFile,
            'error'
        );
        $streamHandler->setFormatter($lineFormatter);

        $logger = new Logger('test logger');
        $logger->pushHandler($streamHandler);

        for ($i = 0; $i < $exceptionsToBeLogged; $i++) {
            $logger->error($exception->getMessage(), [$exception]);
        }

        $exceptionLogFileHelper = new \OxidEsales\TestingLibrary\helpers\ExceptionLogFileHelper($exceptionLogFile);
        $actualParsedExceptions = $exceptionLogFileHelper->getParsedExceptions();

        fclose($exceptionLogFileRessource);

        for ($i = 0; $i < $exceptionsToBeLogged; $i++) {
            $this->assertContains($expectedLevel, $actualParsedExceptions[$i]);
            $this->assertContains($expectedType, $actualParsedExceptions[$i]);
            $this->assertContains($expectedMessage, $actualParsedExceptions[$i]);
        }
    }

    public function dataProviderNumberOfExceptionsToBeLogged()
    {
        return [
            [0],
            [1],
            [5],
        ];
    }
}
