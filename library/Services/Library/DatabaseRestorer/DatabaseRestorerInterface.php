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
namespace OxidEsales\TestingLibrary\Services\Library\DatabaseRestorer;

use Exception;

/**
 * Database maintenance class responsible complete for backuping and restoration of test database.
 */
interface DatabaseRestorerInterface
{
    /**
     * Creates a dump of the current database, stored in the file '/tmp/tmp_db_dump'
     * the dump includes the data and sql insert statements
     *
     * @param string $dumpName Only used during database preparation.
     *
     * @throws Exception
     */
    public function dumpDB($dumpName = 'test');

    /**
     * Checks which tables of the db changed and then restores these tables.
     * Uses dump file '/tmp/tmp_db_dump' for comparison and restoring.
     *
     * @param string $dumpName Only used during database preparation.
     */
    public function restoreDB($dumpName = 'test');

    /**
     * Restores table records
     *
     * @param string $table           Table to restore
     * @param bool   $restoreColumns whether to check and restore table columns
     *
     * @return bool whether table had changes
     */
    public function restoreTable($table, $restoreColumns = false);
}
