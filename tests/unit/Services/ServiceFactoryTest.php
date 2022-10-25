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

use org\bovigo\vfs\vfsStream;

use OxidEsales\TestingLibrary\Services\Library\ServiceConfig;
use OxidEsales\TestingLibrary\Services\ServiceFactory;

class ServiceFactoryTest extends PHPUnit\Framework\TestCase
{
    public function testThrowingExceptionWhenServiceNotFound()
    {
        $this->expectException('Exception');
        $message = "Service 'TestService' was not found!";
        $this->expectExceptionMessage($message);

        vfsStream::setup('root', 777, array('bootstrap.php' => ''));

        /** @var ServiceConfig|PHPUnit\Framework\MockObject\MockObject $config */
        $config = $this->getMockBuilder(OxidEsales\TestingLibrary\Services\Library\ServiceConfig::class)
        ->setMethods(['getServicesDirectory', 'getShopDirectory'])
            ->disableOriginalConstructor()
        ->getMock();
        $config->expects($this->any())->method('getServicesDirectory')->will($this->returnValue(vfsStream::url('root')));
        $config->expects($this->any())->method('getShopDirectory')->will($this->returnValue(vfsStream::url('root')));

        $serviceFactory = new ServiceFactory($config);
        $serviceFactory->createService('TestService');
    }
}