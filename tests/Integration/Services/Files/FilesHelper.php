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

namespace OxidEsales\TestingLibrary\Tests\Integration\Services\Files;

use org\bovigo\vfs\vfsStream;

/**
 * Wrapper for a vfsStream.
 * Simplifies creation/usages of virtual directory structure.
 */
class FilesHelper
{
    /**
     * Simplifies creation of virtual directory structure.
     *
     * @param array $structure possibility to provide custom structure.
     * @param string $rootDirectoryName possibility to provide custom name for a file root directory.
     * @param int $rights possibility to provide custom rights for a files.
     *
     * @return string
     */
    static function prepareStructureAndReturnPath($structure = [], $rootDirectoryName = 'root', $rights = 0777)
    {
        $vfsStream = vfsStream::setup($rootDirectoryName, $rights, $structure);
        $rootPath = $vfsStream->url();

        return $rootPath;
    }
}
