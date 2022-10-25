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
class Cache
{
    /**
     * Clears cache backend.
     */
    public function clearCacheBackend()
    {
    }

    /**
     * Clears reverse proxy cache.
     */
    public function clearReverseProxyCache()
    {
        if (class_exists('\OxidEsales\VarnishModule\Core\OeVarnishModule', false)) {
            \OxidEsales\VarnishModule\Core\OeVarnishModule::flushReverseProxyCache();
        }
    }

    /**
     * Clears temporary directory.
     */
    public function clearTemporaryDirectory()
    {
        if ($sCompileDir = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\ConfigFile::class)->getVar('sCompileDir')) {
            if (!is_writable($sCompileDir)) {
                CliExecutor::executeCommand("sudo chmod -R 777 $sCompileDir");
            }
            $this->removeTemporaryDirectory($sCompileDir, false);
        }
    }

    /**
     * Delete all files and dirs recursively
     *
     * @param string $dir       Directory to delete
     * @param bool   $rmBaseDir Keep target directory
     */
    private function removeTemporaryDirectory($dir, $rmBaseDir = false)
    {
        $itemsToIgnore = array('.', '..', '.htaccess');

        $files = array_diff(scandir($dir), $itemsToIgnore);
        foreach ($files as $file) {
            if (is_dir("$dir/$file")) {
                $this->removeTemporaryDirectory(
                    "$dir/$file",
                    $file == 'smarty' ? $rmBaseDir : true
                );
            } else {
                @unlink("$dir/$file");
            }
        }
        if ($rmBaseDir) {
            @rmdir($dir);
        }
    }
}
