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
namespace OxidEsales\TestingLibrary\Services\Library;



/**
 * Class used for uploading files in services.
 */
class ServiceConfig
{
    const EDITION_COMMUNITY = 'CE';

    /** @var string Tested shop directory. */
    private $shopDirectory;

    /** @var string Shop edition. */
    private $shopEdition;

    /** @var string Temporary directory to store temp files. */
    private $tempDirectory;

    /**
     * Sets default values.
     *
     * @param string $shopDirectory
     * @param string $tempDirectory
     */
    public function __construct($shopDirectory, $tempDirectory = '')
    {
        $this->shopDirectory = $shopDirectory;
        if (empty($tempDirectory)) {
            $tempDirectory = $shopDirectory . '/temp';
        }
        $this->tempDirectory = $tempDirectory;
    }

    /**
     * Returns path to shop source directory.
     * If shop path was not set, it assumes that services was copied to shop root directory.
     *
     * @return string
     */
    public function getShopDirectory()
    {
        return $this->shopDirectory;
    }

    /**
     * Sets shop path.
     *
     * @param string $shopDirectory
     */
    public function setShopDirectory($shopDirectory)
    {
        $this->shopDirectory = $shopDirectory;
    }


    /**
     * Returns shop edition
     *
     * @return array|null|string
     */
    public function getShopEdition()
    {
        if (is_null($this->shopEdition)) {
            $config = new \OxidEsales\Eshop\Core\Config();
            $shopEdition = $config->getEdition();

            $this->shopEdition = strtoupper($shopEdition);
        }
        return $this->shopEdition;
    }

    /**
     * Sets shop path.
     *
     * @param string $shopEdition
     */
    public function setShopEdition($shopEdition)
    {
        $this->shopEdition = $shopEdition;
    }

    /**
     * Returns temp path.
     *
     * @return string
     */
    public function getTempDirectory()
    {
        if (!file_exists($this->tempDirectory)) {
            mkdir($this->tempDirectory, 0777);
            chmod($this->tempDirectory, 0777);
        }

        return $this->tempDirectory;
    }

    /**
     * Set temp path.
     *
     * @param string $tempPath
     */
    public function setTempDirectory($tempPath)
    {
        $this->tempDirectory = $tempPath;
    }

    /**
     * Returns services root directory.
     *
     * @return string
     */
    public function getServicesDirectory()
    {
        return __DIR__ .'/../';
    }
}
