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

use OxidEsales\TestingLibrary\Services\Library\Request;
use OxidEsales\TestingLibrary\Services\Library\ServiceConfig;
use OxidEsales\TestingLibrary\Services\ServiceFactory;

error_reporting(E_ALL);
ini_set('display_errors', '1');

spl_autoload_register(function($className) {
    if (strpos($className, 'OxidEsales\\TestingLibrary\\Services\\') !== false) {
        $class = substr($className, 35);
        $filePath = __DIR__.'/'.str_replace('\\', '/', $class).'.php';
        if (file_exists($filePath)) {
            include_once $filePath;
        }
    }
});

// We need the composer autoloader.
$installationRootPath =  dirname(dirname(dirname(__FILE__)));
$vendorDirectory = $installationRootPath . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR;
require_once $vendorDirectory . 'autoload.php';

// Generate UNC classes before bootstrapping the shop
\OxidEsales\TestingLibrary\TestConfig::prepareUnifiedNamespaceClasses();

// Bootstrap the shop framework
require_once __DIR__ ."../../bootstrap.php";

/** This constant should only be used in TestConfig class. Use TestConfig::getVendorPath() instead. */
define('TEST_LIBRARY_VENDOR_DIRECTORY', $vendorDirectory);

$request = new Request();
$config = new ServiceConfig(__DIR__ . '/../');
$serviceFactory = new ServiceFactory($config);

$service = $serviceFactory->createService($request->getParameter('service'));
$response = $service->init($request);

echo serialize($response);
