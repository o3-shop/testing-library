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

use OxidEsales\EshopCommunity\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\TestingLibrary\Services\Library\DatabaseHandler;

class oxDatabaseHelper
{
    /** @var DatabaseInterface The database to use */
    protected $database;

    public function __construct(DatabaseInterface $database)
    {
        $this->database = $database;
        $this->database->forceMasterConnection();
    }

    /**
     * @param string $tableName
     * @param string $fieldName
     *
     * @return object
     */
    public function getFieldInformation($tableName, $fieldName)
    {
        $columns = $this->database->metaColumns($tableName);

        foreach($columns as $column) {
            if ($column->name === $fieldName) {

                return $column;
            }
        }

        return null;
    }

    /**
     * @param string $tableName
     */
    public function dropView($tableName)
    {
        if ($this->existsView($tableName)) {
            $generator = oxNew(\OxidEsales\Eshop\Core\TableViewNameGenerator::class);
            $tableNameView = $generator->getViewName($tableName, 0);

            $this->database->execute("DROP VIEW " . $this->database->quoteIdentifier($tableNameView));
        }
    }

    /**
     * @param string $tableName
     *
     * @return bool Does the view with the given name exists?
     */
    public function existsView($tableName)
    {
        $generator = oxNew(\OxidEsales\Eshop\Core\TableViewNameGenerator::class);
        $tableNameView = $generator->getViewName($tableName, 0);
        $sql = "SHOW TABLES LIKE '$tableNameView'";

        return $tableNameView === $this->database->getOne($sql);
    }

    /**
     * @param string $tableName The name of the table we want to assure to exist.
     *
     * @return bool Does the database table with the given name exists?
     */
    public function existsTable($tableName)
    {
        $sql = "SELECT COUNT(TABLE_NAME) FROM information_schema.TABLES WHERE TABLE_NAME = '$tableName'";

        $count = $this->database->getOne($sql);

        return $count > 0;
    }

    public function adjustTemplateBlocksOxModuleColumn()
    {
        $sql = "ALTER TABLE `oxtplblocks` 
          CHANGE `OXMODULE` `OXMODULE` char(32) 
          character set latin1 collate latin1_general_ci NOT NULL 
          COMMENT 'Module, which uses this template';";

        $this->database->execute($sql);
    }

    public function getDataBaseTables()
    {
        $shopConfigFile = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\ConfigFile::class);
        $databaseHandler = new DatabaseHandler($shopConfigFile);

        $sql = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . $databaseHandler->getDbName() . "'";

        return $this->database->getAll($sql);
    }
}
