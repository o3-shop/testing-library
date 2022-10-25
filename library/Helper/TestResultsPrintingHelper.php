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

namespace OxidEsales\TestingLibrary\Helper;

/**
 * @internal
 */
class TestResultsPrintingHelper
{
    private const TIMESTAMP_FORMAT = 'Y-m-d-H-i-s';
    private const TIMESTAMP_INSERTION_MARKER = 'TIMESTAMP';

    public function getReportFileName(string $extension = 'xml'): string
    {
        return sprintf('report_%s.%s', $this->getUniqueTimestamp(), $extension);
    }

    public function insertReportTimestamps(string $command): string
    {
        return str_replace(self::TIMESTAMP_INSERTION_MARKER, $this->getUniqueTimestamp(), $command);
    }

    private function getUniqueTimestamp(): string
    {
        return sprintf('%s_%s', date(self::TIMESTAMP_FORMAT), uniqid());
    }
}
