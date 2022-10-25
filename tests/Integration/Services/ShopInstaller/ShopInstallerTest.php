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

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\TestingLibrary\ServiceCaller;
use OxidEsales\TestingLibrary\Services\Library\DatabaseHandler;
use OxidEsales\TestingLibrary\TestConfig;

require_once TEST_LIBRARY_HELPERS_PATH . 'oxDatabaseHelper.php';

class ShopInstallerTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    const DEFAULT_OXMODULE_COLUMN_MAX_LENGTH = 32;
    const CHANGED_OXMODULE_COLUMN_MAX_LENGTH = 100;

    public function testShopInstallerCallsMigrationsAndRegeneratesViews()
    {
        $this->checkBeforeInstall();

        // to be able to assert afterwards, that the views generation was called, we delete one view here
        $this->dropOxDiscountView();

        try {
            $serviceCaller = new ServiceCaller(new TestConfig());

            $serviceCaller->callService('ShopInstaller');
        } catch (\Exception $e) {
            exit("Failed to install shop with message:" . $e->getMessage());
        }

        $this->checkAfterInstall();
    }

    /**
     * To be able to assure, that the ShopInstall service call worked correct, we check before, if everything is well.
     */
    protected function checkBeforeInstall()
    {
        $databaseHelper = new oxDatabaseHelper(DatabaseProvider::getDb());
        $databaseHelper->adjustTemplateBlocksOxModuleColumn();

        $this->assertThereExistsAtLeastOneDatabaseTable();
        $this->assertOxModuleColumnHasMaxLength(self::DEFAULT_OXMODULE_COLUMN_MAX_LENGTH);
        $this->assureGenerateViewsWasCalled();
    }

    /**
     * To assure, that the ShopInstall service call worked correct, we check, that
     *  - the views are regenerated
     *  - the migrations where called
     */
    protected function checkAfterInstall()
    {
        $this->assureMigrationWasCalled();
        $this->assureGenerateViewsWasCalled();
    }

    /**
     * @param int $expectedMaxLength
     */
    private function assertOxModuleColumnHasMaxLength($expectedMaxLength)
    {
        $databaseHelper = new oxDatabaseHelper(DatabaseProvider::getDb());

        $columnInformation = $databaseHelper->getFieldInformation('oxtplblocks', 'OXMODULE');

        $this->assertEquals($expectedMaxLength, $columnInformation->max_length);
    }

    protected function dropOxDiscountView()
    {
        $databaseHelper = new oxDatabaseHelper(DatabaseProvider::getDb());

        $databaseHelper->dropView('oxdiscount');

        $this->assertViewNotExists('oxdiscount');
    }

    private function assureMigrationWasCalled()
    {
        $this->assertOxModuleColumnHasMaxLength(self::CHANGED_OXMODULE_COLUMN_MAX_LENGTH);
    }

    protected function assureGenerateViewsWasCalled()
    {
        $this->assertViewExists('oxdiscount');
    }

    protected function assertThereExistsAtLeastOneDatabaseTable()
    {
        $databaseHelper = new oxDatabaseHelper(DatabaseProvider::getDb());

        $this->assertNotEmpty($databaseHelper->getDataBaseTables());
    }
}
