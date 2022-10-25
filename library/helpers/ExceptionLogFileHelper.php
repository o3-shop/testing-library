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

namespace OxidEsales\TestingLibrary\helpers;

/**
 * Class ExceptionLogFileHelper
 *
 * This class contains helper methods to deal with the exception log file in the tests
 *
 * @package OxidEsales\TestingLibrary\helpers
 */
class ExceptionLogFileHelper
{
    /**
     * @var string The fully qualified path to the exception log file
     */
    protected $exceptionLogFile;

    /**
     * ExceptionLogHelper constructor.
     *
     * @param string $exceptionLogFile The fully qualified path to the exception log file
     *
     * @throws \OxidEsales\Eshop\Core\Exception\StandardException
     */
    public function __construct($exceptionLogFile)
    {
        if (!$exceptionLogFile || !is_string($exceptionLogFile)) {
            throw new \OxidEsales\Eshop\Core\Exception\StandardException('Constructor parameter $exceptionLogFile must be a non empty string');
        }
        $this->exceptionLogFile = $exceptionLogFile;
    }

    /**
     * Return the complete content of the exception log file as a string.
     *
     * @return string Content of the exception log file
     *
     * @throws \OxidEsales\Eshop\Core\Exception\StandardException if log file contend could not be read
     */
    public function getExceptionLogFileContent()
    {
        $fileCreated = false;
        
        /** Suppress the warning, which is emitted, if the file does not exist */
        if ($fileDoesNotExist = !@file_exists($this->exceptionLogFile)) {
            $fileCreated = touch($this->exceptionLogFile);
        }
        if ($fileDoesNotExist && !$fileCreated) {
            throw new \OxidEsales\Eshop\Core\Exception\StandardException('Empty file ' . $this->exceptionLogFile . ' could not have been be created');
        }

        $logFileContent = file_get_contents($this->exceptionLogFile);
        if (false === $logFileContent) {
            throw new \OxidEsales\Eshop\Core\Exception\StandardException('File ' . $this->exceptionLogFile . ' could not be read');
        }

        return $logFileContent;
    }

    /**
     * Use this method in _justified_ cases to clear exception log, e.g. if you are testing  exceptions and their behavior.
     * Do _not_ use this method to silence exceptions, if you do not understand why they are thrown or if you are too lazy to fix the root cause.
     *
     * @throws \OxidEsales\Eshop\Core\Exception\StandardException
     */
    public function clearExceptionLogFile()
    {
        if (!$filePointerResource = fopen($this->exceptionLogFile, 'w')) {
            throw new \OxidEsales\Eshop\Core\Exception\StandardException('File ' . $this->exceptionLogFile . ' could not be opened in write mode');
        }
        if (!fclose($filePointerResource)) {
            throw new \OxidEsales\Eshop\Core\Exception\StandardException('File pointer resource for file ' . $this->exceptionLogFile . ' could not be closed');
        };
    }


    /**
     * Return an array of arrays with parsed exception lines
     *
     * @return array
     */
    public function getParsedExceptions()
    {
        $parsedExceptions = [];

        $exceptions = $this->getExceptionLinesFromLogFile();
        foreach ($exceptions as $exception) {
            $parsedExceptions[] = str_replace('\\\\', '\\', $exception);
        }

        return $parsedExceptions;
    }

    /**
     * Return an array, which only contains the lines with information about the exception, not the whole stacktrace
     *
     * @return array
     *
     * @throws \OxidEsales\Eshop\Core\Exception\StandardException
     */
    protected function getExceptionLinesFromLogFile()
    {
        $exceptionLogLines = file($this->exceptionLogFile, FILE_IGNORE_NEW_LINES);
        if (false === $exceptionLogLines) {
            throw new \OxidEsales\Eshop\Core\Exception\StandardException('File ' . $this->exceptionLogFile . ' could not be read');
        }

        $exceptionEntries = array_filter(
            $exceptionLogLines,
            function ($entry) {
                return false !== strpos($entry, '.ERROR') && false !== strpos($entry, 'Exception');
            }
        );

        return $exceptionEntries;
    }
}
