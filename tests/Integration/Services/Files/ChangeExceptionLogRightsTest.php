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

namespace OxidEsales\TestingLibrary\Tests\Integration\Services\Files;

use OxidEsales\TestingLibrary\Services\Files\ChangeExceptionLogRights;
use OxidEsales\TestingLibrary\Services\Library\Request;
use OxidEsales\TestingLibrary\Services\Library\ServiceConfig;
use PHPUnit\Framework\TestCase;

class ChangeExceptionLogRightsTest extends TestCase
{
    public function testLogIsWritableForAllUsersWhenFileExist()
    {
        $rootPath = FilesHelper::prepareStructureAndReturnPath(['log' => ['oxid.log' => 'content']]);
        $pathToExceptionLog = "$rootPath/log/oxideshop.log";
        chmod($pathToExceptionLog, 0111);

        $this->assertFalse(is_writable($pathToExceptionLog));

        $changeRightsService = new ChangeExceptionLogRights(new ServiceConfig($rootPath));
        $changeRightsService->init($request = new Request());

        $filePermissions = $this->getFilePermissions($pathToExceptionLog);
        $this->assertSame('0777', $filePermissions, 'Exception log should be writable.');
    }

    public function testCreateWhenFileDoesNotExist()
    {
        $rootPath = FilesHelper::prepareStructureAndReturnPath(['log' => []]);
        $pathToExceptionLog = "$rootPath/log/oxideshop.log";

        $this->assertFalse(file_exists($pathToExceptionLog));

        $changeRightsService = new ChangeExceptionLogRights(new ServiceConfig($rootPath));
        $changeRightsService->init($request = new Request());

        $this->assertTrue(file_exists($pathToExceptionLog));
        $filePermissions = $this->getFilePermissions($pathToExceptionLog);
        $this->assertSame('0777', $filePermissions, 'Exception log should be writable.');
    }

    /**
     * Return file permissions in a normal form.
     *
     * @param string $pathToFile
     *
     * @return string
     */
    private function getFilePermissions($pathToFile)
    {
        return substr(sprintf('%o', fileperms($pathToFile)), -4);
    }
}
