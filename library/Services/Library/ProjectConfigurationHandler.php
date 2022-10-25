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

namespace OxidEsales\TestingLibrary\Services\Library;

use OxidEsales\TestingLibrary\Helper\ProjectConfigurationHelperInterface;
use OxidEsales\TestingLibrary\Services\Library\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

/**
 * @internal
 */
class ProjectConfigurationHandler
{
    /**
     * @var ProjectConfigurationHelperInterface
     */
    private $configurationHelper;

    public function __construct(ProjectConfigurationHelperInterface $configurationHelper)
    {
        $this->configurationHelper = $configurationHelper;
    }

    /**
     * Backup project configuration.
     * @throws FileNotFoundException
     */
    public function backup()
    {
        if (!file_exists($this->getOriginalConfigurationPath())) {
            throw new FileNotFoundException('Unable to backup ' . $this->getOriginalConfigurationPath() . '. It does not exist.');
        }
        $this->recursiveCopy($this->getOriginalConfigurationPath(), $this->getBackupConfigurationPath());
    }

    /**
     * Restore project configuration.
     * @throws FileNotFoundException
     */
    public function restore()
    {
        if (!file_exists($this->getBackupConfigurationPath())) {
            throw new FileNotFoundException('Unable to restore ' . $this->getBackupConfigurationPath() . '. It does not exist.');
        }
        $this->rmdirRecursive($this->getOriginalConfigurationPath());
        $this->recursiveCopy($this->getBackupConfigurationPath(), $this->getOriginalConfigurationPath());
    }

    /**
     * Deletes project configuration backup file.
     * @throws FileNotFoundException
     *
     * @deprecated 7.3.0
     */
    public function cleanup()
    {
        if (!file_exists($this->getBackupConfigurationPath())) {
            throw new FileNotFoundException('Unable to delete ' . $this->getBackupConfigurationPath() . '. It does not exist.');
        }
        $this->rmdirRecursive($this->getBackupConfigurationPath());
    }

    /**
     * @return string
     */
    private function getOriginalConfigurationPath(): string
    {
        return Path::join($this->configurationHelper->getConfigurationDirectoryPath());
    }

    /**
     * @return string
     */
    private function getBackupConfigurationPath(): string
    {
        return Path::join($this->configurationHelper->getConfigurationDirectoryPath() . '-backup');
    }

    /**
     * @param string $source
     * @param string $destination
     */
    private function recursiveCopy(string $source, string $destination) : void
    {
        $filesystem = new Filesystem();
        $filesystem->mirror($source, $destination);
    }

    /**
     * @param string $directory
     */
    private function rmdirRecursive(string $directory): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove($directory);
    }
}
