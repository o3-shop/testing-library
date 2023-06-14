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

require_once 'base.php';

require_once TEST_LIBRARY_PATH .'Deprecated.php';

// NOTE: Presence of the correct UNC classes needs to be ensured before any shop classes can be used.
\OxidEsales\TestingLibrary\TestConfig::prepareUnifiedNamespaceClasses();

define('OXID_PHP_UNIT', true);

$sTestFilePath = strtolower(implode(",", $_SERVER['argv']));
$sTestType = 'unit';
foreach (array('acceptance', 'selenium', 'javascript') as $search) {
    if (strpos($sTestFilePath, $search) !== false) {
        $sTestType = 'acceptance';
        break;
    }
}

switch($sTestType) {
    case 'acceptance':
        $bootstrap = new OxidEsales\TestingLibrary\Bootstrap\SeleniumBootstrap();
        break;
    default:
        $bootstrap = new OxidEsales\TestingLibrary\Bootstrap\UnitBootstrap();
        break;
}

$bootstrap->init();
