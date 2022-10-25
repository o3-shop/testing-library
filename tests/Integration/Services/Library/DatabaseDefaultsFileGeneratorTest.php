<?php declare(strict_types=1);
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

use OxidEsales\TestingLibrary\Services\Library\DatabaseDefaultsFileGenerator;

class DatabaseDefaultsFileGeneratorTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    public function testFileGeneration()
    {
        $user = 'testUser';
        $password = 'testPassword';
        $host = 'testHost';
        $port = '1111';

        $configFile = $this->getMockBuilder('OxidEsales\Eshop\Core\ConfigFile')
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();
        $configFile->dbUser = $user;
        $configFile->dbPwd = $password;
        $configFile->dbHost = $host;
        $configFile->dbPort = $port;
        $generator = new DatabaseDefaultsFileGenerator($configFile);
        $file = $generator->generate();
        $fileContents = file_get_contents($file);

        $this->assertTrue((bool)strpos($fileContents, $user));
        $this->assertTrue((bool)strpos($fileContents, $password));
        $this->assertTrue((bool)strpos($fileContents, $host));
        $this->assertTrue((bool)strpos($fileContents, $port));

        unlink($file);
    }
}
