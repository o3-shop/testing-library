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

use OxidEsales\TestingLibrary\Services\Files\Remove;
use OxidEsales\TestingLibrary\Services\Library\Request;
use OxidEsales\TestingLibrary\Services\Library\ServiceConfig;

class RemoveTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    public function testRemoveWhenNoFilesProvided()
    {
        $rootPath = FilesHelper::prepareStructureAndReturnPath($this->getDirectoryStructure());
        $this->initializeFilesRemoval($rootPath, []);
        $this->assertTrue(file_exists($rootPath.'/testDirectory/someFile.php'), "$rootPath/testDirectory/someFile.php");
        $this->assertTrue(file_exists($rootPath.'/testDirectory/someFile2.php'), "$rootPath/testDirectory/someFile2.php");
    }

    public function testRemoveFile()
    {
        $rootPath = FilesHelper::prepareStructureAndReturnPath($this->getDirectoryStructure());
        $this->initializeFilesRemoval($rootPath, [$rootPath.'/testDirectory/someFile.php']);
        $this->assertFalse(file_exists($rootPath.'/testDirectory/someFile.php'), "$rootPath/testDirectory/someFile.php");
        $this->assertTrue(file_exists($rootPath.'/testDirectory/someFile2.php'), "$rootPath/testDirectory/someFile2.php");
    }

    /**
     * @param string $rootPath
     * @param array $files
     */
    protected function initializeFilesRemoval($rootPath, $files)
    {
        $removeService = new Remove(new ServiceConfig($rootPath));
        $request = new Request([Remove::FILES_PARAMETER_NAME => $files]);
        $removeService->init($request);
    }

    /**
     * Get directory structure to mock for the tests.
     *
     * @return array
     */
    private function getDirectoryStructure()
    {
        return [
            'testDirectory' => [
                'someFile.php' => 'content',
                'someFile2.php' => 'content',
            ]
        ];
    }
}
