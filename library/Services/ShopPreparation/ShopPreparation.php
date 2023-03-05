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
namespace OxidEsales\TestingLibrary\Services\ShopPreparation;

use OxidEsales\TestingLibrary\Services\Library\DatabaseRestorer\DatabaseRestorerFactory;
use OxidEsales\TestingLibrary\Services\Library\DatabaseRestorer\DatabaseRestorerInterface;
use OxidEsales\TestingLibrary\Services\Library\DatabaseHandler;
use OxidEsales\TestingLibrary\Services\Library\DatabaseRestorer\DatabaseRestorerToFile;
use OxidEsales\TestingLibrary\Services\Library\Request;
use OxidEsales\TestingLibrary\Services\Library\ServiceConfig;
use OxidEsales\TestingLibrary\Services\Library\ShopServiceInterface;

/**
 * Shop constructor class for modifying shop environment during testing
 * Class ShopConstructor
 */
class ShopPreparation implements ShopServiceInterface
{
    /** @var DatabaseHandler Database communicator object */
    private $databaseHandler = null;

    /** @var DatabaseRestorerInterface Database communicator object */
    private $databaseRestorer = null;

    /**
     * Initiates class dependencies.
     *
     * @param ServiceConfig $config
     */
    public function __construct($config)
    {
        $configFile = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\ConfigFile::class);
        $this->databaseHandler = new DatabaseHandler($configFile, $config->getTempDirectory());

        $factory = new DatabaseRestorerFactory();
        $this->databaseRestorer = $factory->createRestorer(DatabaseRestorerToFile::class);
    }

    /**
     * Handles request parameters.
     *
     * @param Request $request
     */
    public function init($request)
    {
        if ($file = $request->getUploadedFile('importSql')) {
            $databaseHandler = $this->getDatabaseHandler();
            $databaseHandler->import($file);
        }

        if ($request->getParameter('dumpDB')) {
            $databaseRestorer = $this->getDatabaseRestorer();
            $databaseRestorer->dumpDB($request->getParameter('dump-prefix'));
        }

        if ($request->getParameter('restoreDB')) {
            $databaseRestorer = $this->getDatabaseRestorer();
            $databaseRestorer->restoreDB($request->getParameter('dump-prefix'));
        }
    }

    /**
     * @return DatabaseHandler
     */
    protected function getDatabaseHandler()
    {
        return $this->databaseHandler;
    }

    /**
     * @return DatabaseRestorerInterface
     */
    protected function getDatabaseRestorer()
    {
        return $this->databaseRestorer;
    }
}
