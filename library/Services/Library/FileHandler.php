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
class FileHandler
{
    /**
     * Creates directory with write permissions
     *
     * @param string $directoryPath
     * @param int    $permissions
     */
    public function createDirectory($directoryPath, $permissions = 0777)
    {
        $current = '';
        $parts = array_filter(explode('/', $directoryPath));
        foreach ($parts as $part) {
            $current = "$current/$part";
            if (!empty($part) && !file_exists($current)) {
                mkdir($current, $permissions);
                chmod($current, $permissions);
            }
        }
    }
}
