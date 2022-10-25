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

use org\bovigo\vfs\vfsStream;
use OxidEsales\TestingLibrary\Helper\ProjectConfigurationHelperInterface;
use OxidEsales\TestingLibrary\Services\Library\Exception\FileNotFoundException;
use OxidEsales\TestingLibrary\Services\Library\ProjectConfigurationHandler;
use PHPUnit\Framework\TestCase;
use Webmozart\PathUtil\Path;

class ProjectConfigurationHandlerTest extends TestCase
{
    private $configurationDirectory;
    private $configurationFileInSubDirectory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareVfsStructure();
    }

    public function testFileBackup()
    {
        $projectConfigurationHelperStub = $this->makeProjectConfigurationHelperStub();

        $handler = new ProjectConfigurationHandler($projectConfigurationHelperStub);
        $handler->backup();

        $this->assertFileExists($this->getBackupConfigurationFile());
    }

    public function testFolderBackupWithoutFile()
    {
        $projectConfigurationHelperStub = $this->makeProjectConfigurationHelperStub();

        unlink($this->configurationFileInSubDirectory);
        $handler = new ProjectConfigurationHandler($projectConfigurationHelperStub);
        $handler->backup();

        $this->assertDirectoryExists($this->getConfigurationBackupDirectory());
    }

    public function testFileRestoration()
    {
        $projectConfigurationHelperStub = $this->makeProjectConfigurationHelperStub();

        $handler = new ProjectConfigurationHandler($projectConfigurationHelperStub);
        $handler->backup();
        unlink($this->configurationFileInSubDirectory);
        $handler->restore();

        $this->assertFileExists($this->getBackupConfigurationFile());
    }

    public function testFolderRestorationWhenItDoesNotExist()
    {
        $this->expectException(FileNotFoundException::class);

        $projectConfigurationHelperStub = $this->makeProjectConfigurationHelperStub();

        $handler = new ProjectConfigurationHandler($projectConfigurationHelperStub);
        $handler->restore();
    }

    public function testFolderCleanup()
    {
        $projectConfigurationHelperStub = $this->makeProjectConfigurationHelperStub();

        $handler = new ProjectConfigurationHandler($projectConfigurationHelperStub);
        $handler->backup();
        $handler->cleanup();

        $this->assertFileNotExists($this->getBackupConfigurationFile());
    }

    public function testFolderCleanupWhenFileDoesNotExists()
    {
        $this->expectException(FileNotFoundException::class);

        $projectConfigurationHelperStub = $this->makeProjectConfigurationHelperStub();

        $handler = new ProjectConfigurationHandler($projectConfigurationHelperStub);
        $handler->cleanup();
    }

    private function prepareVfsStructure()
    {
        $structure = [
            'configuration' => [
                'shops' => [
                    'configuration.yml' => 'anything',
                ]
            ],
        ];

        $root = vfsStream::setup('root', null, $structure);

        $this->configurationDirectory = vfsStream::url('root/configuration');
        $this->configurationFileInSubDirectory = vfsStream::url(
            'root/configuration/shops/configuration.yml'
        );
    }

    private function getBackupConfigurationFile()
    {
        return vfsStream::url(
            'root/configuration-backup/shops/configuration.yml'
        );
    }

    private function getConfigurationBackupDirectory()
    {
        return vfsStream::url(
            'root/configuration-backup'
        );
    }

    private function makeProjectConfigurationHelperStub(): ProjectConfigurationHelperInterface
    {
        $projectConfigurationHelperStub = $this->getMockBuilder(ProjectConfigurationHelperInterface::class)
            ->getMock();

        $projectConfigurationHelperStub
            ->method('getConfigurationDirectoryPath')
            ->willReturn($this->configurationDirectory);

        return $projectConfigurationHelperStub;
    }
}
