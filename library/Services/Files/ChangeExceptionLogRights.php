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

namespace OxidEsales\TestingLibrary\Services\Files;

use OxidEsales\TestingLibrary\Services\Library\ServiceConfig;
use OxidEsales\TestingLibrary\Services\Library\ShopServiceInterface;
use Symfony\Component\Filesystem\Filesystem;


/**
 * Calling service with different user might create exception log
 * which is not writable for apache user.
 * Update rights so apache user could always write to log.
 * Create log as apache user would create it unwritable for CLI user.
 */
class ChangeExceptionLogRights implements ShopServiceInterface
{
    /** @var ServiceConfig */
    private $serviceConfig;

    /** @var Filesystem */
    private $fileSystem;

    /** @var String partly path to exception log */
    const EXCEPTION_LOG_PATH = 'log' . DIRECTORY_SEPARATOR . 'oxideshop.log';

    /**
     * Remove constructor.
     * @param ServiceConfig $config
     */
    public function __construct($config)
    {
        $this->serviceConfig = $config;
        $this->fileSystem = new Filesystem();
    }

    /**
     * @param \OxidEsales\TestingLibrary\Services\Library\Request $request
     */
    public function init($request)
    {
        $fileSystem = new Filesystem();

        $pathToExceptionLog = $this->serviceConfig->getShopDirectory()
            . DIRECTORY_SEPARATOR . self::EXCEPTION_LOG_PATH;

        if (!$fileSystem->exists([$pathToExceptionLog])) {
            $fileSystem->touch($pathToExceptionLog);
        }
        $fileSystem->chmod($pathToExceptionLog, 0777);
    }
}
