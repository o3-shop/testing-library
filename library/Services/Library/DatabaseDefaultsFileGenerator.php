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

use OxidEsales\Eshop\Core\ConfigFile;

class DatabaseDefaultsFileGenerator
{
    /**
     * @var ConfigFile
     */
    private $config;

    /**
     * @param ConfigFile $config
     */
    public function __construct(ConfigFile $config)
    {
        $this->config = $config;
    }

    /**
     * @return string File path.
     */
    public function generate(): string
    {
        $file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('testing_lib', true) . '.cnf';
        $resource = fopen($file, 'w');
        $fileContents = "[client]"
            . "\nuser=" . $this->config->dbUser
            . "\npassword=" . $this->config->dbPwd
            . "\nhost=" . $this->config->dbHost
            . "\nport=" . $this->config->dbPort
            . "\n";
        fwrite($resource, $fileContents);
        fclose($resource);
        return $file;
    }
}
