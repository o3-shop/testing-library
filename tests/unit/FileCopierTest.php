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

use org\bovigo\vfs\vfsStream;
use OxidEsales\TestingLibrary\FileCopier;

class FileCopierTest extends PHPUnit\Framework\TestCase
{
    public function testCopyLocalFile()
    {
        $expectedCommand = "cp -frT 'source' 'target'";

        /** @var FileCopier|PHPUnit\Framework\MockObject\MockObject $fileCopier */
        $fileCopier = $this->getMockBuilder('\OxidEsales\TestingLibrary\FileCopier')
            ->setMethods(['executeCommand'])
            ->getMock();
        $fileCopier->expects($this->once())->method('executeCommand')->with($this->equalTo($expectedCommand));

        $fileCopier->copyFiles('source', 'target');
    }

    public function testCopyLocalFileWithPermissions()
    {
        $expectedCommand = "cp -frT 'source' 'target' && chmod 777 'target'";

        /** @var FileCopier|PHPUnit\Framework\MockObject\MockObject $fileCopier */
        $fileCopier = $this->getMockBuilder('\OxidEsales\TestingLibrary\FileCopier')
            ->setMethods(['executeCommand'])
            ->getMock();
        $fileCopier->expects($this->once())->method('executeCommand')->with($this->equalTo($expectedCommand));

        $fileCopier->copyFiles('source', 'target', true);
    }

    public function testCopyRemoteFile()
    {
        $expectedCommand = "scp -rp 'source' 'user@host:/target'";

        /** @var FileCopier|PHPUnit\Framework\MockObject\MockObject $fileCopier */
        $fileCopier = $this->getMockBuilder('\OxidEsales\TestingLibrary\FileCopier')
            ->setMethods(['executeCommand'])
            ->getMock();
        $fileCopier->expects($this->once())->method('executeCommand')->with($this->equalTo($expectedCommand));

        $fileCopier->copyFiles('source', 'user@host:/target');
    }

    public function testCopyRemoteFileWithPermissions()
    {
        $expectedCommand = "rsync -rp --perms --chmod=u+rwx,g+rwx,o+rwx 'source' 'user@host:/target'";

        /** @var FileCopier|PHPUnit\Framework\MockObject\MockObject $fileCopier */
        $fileCopier = $this->getMockBuilder('\OxidEsales\TestingLibrary\FileCopier')
            ->setMethods(array('executeCommand'))
            ->getMock();
        $fileCopier->expects($this->once())->method('executeCommand')->with($this->equalTo($expectedCommand));

        $fileCopier->copyFiles('source', 'user@host:/target', true);
    }

    public function testEmptyDirectoryCreationWhenDirectoryDoesNotExist()
    {
        $structure = array(
            'testDirectory' => array()
        );

        vfsStream::setup('root', 777, $structure);

        $newDirectory = vfsStream::url('root/testDirectory/emptyDirectory');

        $fileCopier = new FileCopier();
        $fileCopier->createEmptyDirectory($newDirectory);

        $this->assertTrue(is_dir($newDirectory));
        $this->assertEquals(2, count(scandir($newDirectory)));
    }

    public function testEmptyDirectoryCreationWhenDirectoryExist()
    {
        $structure = array(
            'testDirectory' => array(
                'nonEmptyDirectory' => array(
                    'someFile.php' => 'content',
                    'someFile2.php' => 'content',
                    'directory' => array(
                        'someFile' => 'content'
                    )
                )
            )
        );

        vfsStream::setup('root', 777, $structure);

        $newDirectory = vfsStream::url('root/testDirectory/nonEmptyDirectory');

        $fileCopier = new FileCopier();
        $fileCopier->createEmptyDirectory($newDirectory);

        $this->assertTrue(is_dir($newDirectory));
        $this->assertEquals(2, count(scandir($newDirectory)));
    }
}
