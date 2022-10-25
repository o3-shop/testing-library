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

use Exception;

/**
 * Class used for uploading files in services.
 */
class FileUploader
{
    /**
     * Uploads file to given location.
     *
     * @param string $sFileIndex  File index
     * @param string $sLocation   Location where to put uploaded file
     * @param bool   $blOverwrite Whether to overwrite existing file
     *
     * @throws Exception Throws exception if file with given index does not exist.
     *
     * @return bool Whether upload succeeded
     */
    public function uploadFile($sFileIndex, $sLocation, $blOverwrite = true)
    {
        $aFileInfo = $this->_getFileInfo($sFileIndex);

        if (!$this->_checkFile($aFileInfo)) {
            throw new Exception("File with index '$sFileIndex' does not exist or error occurred while downloading it");
        }

        return $this->_moveUploadedFile($aFileInfo, $sLocation, $blOverwrite);
    }

    /**
     * Checks if file information (name and tmp_name) is set and no errors exists.
     *
     * @param array $fileInfo
     *
     * @return bool
     */
    private function _checkFile($fileInfo)
    {
        $result = isset($fileInfo['name']) && isset($fileInfo['tmp_name']);

        if ($result && isset($fileInfo['error']) && $fileInfo['error']) {
            $result = false;
        }

        return $result;
    }

    /**
     * Returns file information.
     *
     * @param string $fileIndex
     *
     */
    private function _getFileInfo($fileIndex)
    {
        return $_FILES[$fileIndex];
    }

    /**
     * @param array  $fileInfo
     * @param string $location
     * @param bool   $overwrite
     *
     * @return bool
     */
    private function _moveUploadedFile($fileInfo, $location, $overwrite)
    {
        $isDone = false;

        if (!file_exists($location) || $overwrite) {
            $isDone = move_uploaded_file($fileInfo['tmp_name'], $location);

            if ($isDone) {
                $isDone = @chmod($location, 0644);
            }
        }

        return $isDone;
    }
}
