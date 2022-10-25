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

use OxidEsales\Eshop\Core\Edition\EditionRootPathProvider;
use OxidEsales\Eshop\Core\Edition\EditionSelector;

/**
 * Class responsible for providing path to testData directory.
 */
class TestSqlPathProvider
{
    const TEST_SQL_DIRECTORY = 'testSql';

    const TESTS_DIRECTORY = 'tests';

    const ACCEPTANCE_DIRECTORY = 'Acceptance';

    /**
     * @var EditionSelector
     */
    private $editionSelector;

    /**
     * @var string
     */
    private $shopPath = '';

    /**
     * @param EditionSelector $editionSelector
     * @param string $shopPath
     */
    public function __construct($editionSelector, $shopPath)
    {
        $this->editionSelector = $editionSelector;
        $this->shopPath = $shopPath;
    }

    /**
     * Method returns path to test data according edition.
     *
     * @param string $testSuitePath
     *
     * @return string
     */
    public function getDataPathBySuitePath($testSuitePath)
    {
        $pathToTestData = $testSuitePath;

        return $pathToTestData . '/' . static::TEST_SQL_DIRECTORY;
    }

    /**
     * @return EditionSelector
     */
    protected function getEditionSelector()
    {
        return $this->editionSelector;
    }

    /**
     * @return string
     */
    protected function getShopPath()
    {
        return $this->shopPath;
    }

    /**
     * Updates provided path for enterprise edition.
     *
     * @param string $pathToTestSql
     *
     * @return string
     */
    protected function updatePathToTestSql($pathToTestSql)
    {
        $pathParts = explode(static::TESTS_DIRECTORY . '/' . static::ACCEPTANCE_DIRECTORY, $pathToTestSql);
        if (count($pathParts) > 1) {
            $testDirectoryName = $pathParts[count($pathParts) - 1];
            $enterprisePathProvider = new EditionRootPathProvider($this->getEditionSelector());
            $pathToEditionTestDirectory =
                $enterprisePathProvider->getDirectoryPath()
                . '/' . ucfirst(static::TESTS_DIRECTORY)
                . '/' . ucfirst(static::ACCEPTANCE_DIRECTORY)
                . '/' . $testDirectoryName;

            $pathToTestSql = realpath($pathToEditionTestDirectory);
        }

        return $pathToTestSql;
    }
}
