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

namespace OxidEsales\TestingLibrary\Services\ProjectConfiguration;

use OxidEsales\TestingLibrary\Helper\ProjectConfigurationHelper;
use OxidEsales\TestingLibrary\Services\Library\Exception\FileNotFoundException;
use OxidEsales\TestingLibrary\Services\Library\Request;
use OxidEsales\TestingLibrary\Services\Library\ShopServiceInterface;
use OxidEsales\TestingLibrary\Services\Library\ProjectConfigurationHandler;
use OxidEsales\TestingLibrary\Services\Library\ServiceConfig;

/**
 * @deprecated 7.3.0
 */
class ProjectConfiguration implements ShopServiceInterface
{
    /**
     * @var $projectConfiguration
     */
    private $projectConfiguration;

    /**
     * Initiates service requirements.
     *
     * @param ServiceConfig $config
     */
    public function __construct($config)
    {
        $this->projectConfiguration = new ProjectConfigurationHandler(new ProjectConfigurationHelper());
    }

    /**
     * Initiates service.
     *
     * @param Request $request
     */
    public function init($request)
    {
        if ($request->getParameter('backup')) {
            $this->projectConfiguration->backup();
        }

        if ($request->getParameter('restore')) {
            $this->projectConfiguration->restore();
        }

        if ($request->getParameter('cleanup')) {
            try {
                $this->projectConfiguration->cleanup();
            } catch (FileNotFoundException $exception) {}
        }
    }
}
