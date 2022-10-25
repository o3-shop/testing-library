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

use OxidEsales\Eshop\Core\Edition\EditionSelector;
use OxidEsales\TestingLibrary\TestSqlPathProvider;

class TestSqlPathProviderTest extends PHPUnit\Framework\TestCase
{
    public function providerChecksForCorrectPath()
    {
        return [
            [
                '/var/www/oxideshop/tests/Acceptance/Admin',
                EditionSelector::COMMUNITY,
                '/var/www/oxideshop/tests/Acceptance/Admin/testSql'
            ]
        ];
    }

    /**
     * @param string $testSuitePath
     * @param string $edition
     * @param string $resultPath
     *
     * @dataProvider providerChecksForCorrectPath
     */
    public function testChecksForCorrectPath($testSuitePath, $edition, $resultPath)
    {
        $shopPath = '/var/www/oxideshop/source';
        $editionSelector = new EditionSelector($edition);
        $testDataPathProvider = new TestSqlPathProvider($editionSelector, $shopPath);

        $this->assertSame($resultPath, $testDataPathProvider->getDataPathBySuitePath($testSuitePath));
    }
}
