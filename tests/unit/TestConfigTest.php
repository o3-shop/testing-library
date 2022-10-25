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

namespace OxidEsales\TestingLibrary\Unit;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

/**
 * Class TestConfigTest
 *
 * @package OxidEsales\TestingLibrary\Unit
 * @covers \OxidEsales\TestingLibrary\TestConfig
 */
class TestConfigTest extends TestCase
{

    /**
     * @covers \OxidEsales\TestingLibrary\TestConfig::getModuleTestSuites()
     */
    public function testGetModuleTestSuites()
    {
        $this->buildModuleStructureWithTwoModules();

        $testConfig = $this->getMockBuilder('\OxidEsales\TestingLibrary\TestConfig')->setMethods([
            'shouldRunModuleTests',
            'getPartialModulePaths',
            'getShopPath'

        ])->getMock();
        $testConfig->expects($this->any())->method('shouldRunModuleTests')->will($this->returnValue(true));
        $testConfig->expects($this->any())->method('getPartialModulePaths')->will(
            $this->returnValue(
                [
                    'myvendor/namespacedModule',
                    'myvendor/plainModule'
                ]
            )
        );

        $shopPath = vfsStream::url('root/');
        $testConfig->expects($this->any())->method('getShopPath')->will($this->returnValue($shopPath));

        $this->assertEquals(
            [
                vfsStream::url('root/modules/myvendor/namespacedModule/Tests/'),
                vfsStream::url('root/modules/myvendor/plainModule/tests/')
            ],
            $testConfig->getModuleTestSuites(),
            "Directories for modules test suites are not found properly."

        );
    }

    private function buildModuleStructureWithTwoModules()
    {
        $structure = [
            'modules' => [
                'myvendor' => [
                    'namespacedModule' => [
                        'Tests' => [
                            'Acceptance'
                        ]
                    ],
                    'plainModule'      => [
                        'tests' => [
                            'Acceptance'
                        ]
                    ]
                ]
            ]
        ];
        vfsStream::setup('root', null, $structure);
    }

}