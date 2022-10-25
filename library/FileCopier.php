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

namespace OxidEsales\TestingLibrary;

use Exception;

/**
 * Class for copying files. Can copy files locally or to external server.
 */
class FileCopier
{

    /**
     * Copy files to shop
     *
     * @param string $source          File or directory to copy.
     * @param string $target          Path where to copy.
     * @param bool   $setPermissions Whether to set given Target permissions to 777.
     */
    public function copyFiles($source, $target, $setPermissions = false)
    {
        if (strpos($target, ':') !== false && strpos($target, '@') !== false) {
            if (is_dir($source)) {
                $source .= "/.";
            }
            $command = "scp -rp " . escapeshellarg($source) . " " . escapeshellarg($target);
            if ($setPermissions) {
                $command = "rsync -rp --perms --chmod=u+rwx,g+rwx,o+rwx " . escapeshellarg($source) . " " . escapeshellarg($target);
            }
        } else {
            $command = "cp -frT " . escapeshellarg($source) . " " . escapeshellarg($target);
            if ($setPermissions) {
                $command .= " && chmod 777 " . escapeshellarg($target);
            }
        }
        $this->executeCommand($command);
    }

    /**
     * Creates new directory if it does not exists, if exists - clears its content.
     *
     * @param string $directory
     */
    public function createEmptyDirectory($directory)
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        } else {
            $this->deleteTree($directory, false);
        }
    }

    /**
     * Deletes given directory content
     *
     * @param string $directory       Path to directory.
     * @param bool   $removeBaseDir Whether to delete base directory.
     */
    protected function deleteTree($directory, $removeBaseDir = false)
    {
        $files = array_diff(scandir($directory), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$directory/$file")) ? $this->deleteTree("$directory/$file", true) : @unlink("$directory/$file");
        }

        if ($removeBaseDir) {
            @rmdir($directory);
        }
    }

    /**
     * Executes shell command.
     *
     * @param string $command
     *
     * @throws Exception
     *
     * @return string Output of command.
     */
    protected function executeCommand($command)
    {
        $result = @exec($command, $output, $code);
        $output = implode("\n", $output);

        if ($result === false) {
            throw new Exception("Failed to execute command '$command' with message: [$code] '$output'");
        }

        return $output;
    }
}
