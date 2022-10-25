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

/**
 * Helper class for \OxidEsales\Eshop\Application\Model\Voucher
 * @deprecated since v4.0.0
 */
class oxVoucherHelper extends  \OxidEsales\Eshop\Application\Model\Voucher
{
    /** @var bool Whether any of the checks were performed. */
    public static $blCheckWasPerformed = false;

    /**
     * Checks availability without user logged in. Returns array with errors.
     *
     * @param array  $aVouchers array of vouchers
     * @param double $dPrice    current sum (price)
     */
    public function checkVoucherAvailability($aVouchers, $dPrice)
    {
        self::$blCheckWasPerformed = true;
    }

    /**
     * Performs basket level voucher availability check (no need to check if voucher
     * is reserved or so).
     *
     * @param array  $aVouchers array of vouchers
     * @param double $dPrice    current sum (price)
     */
    public function checkBasketVoucherAvailability($aVouchers, $dPrice)
    {
        self::$blCheckWasPerformed = true;
    }

    /**
     * Checks availability for the given user. Returns array with errors.
     *
     * @param object $oUser user object
     */
    public function checkUserAvailability($oUser)
    {
        self::$blCheckWasPerformed = true;
    }

    /**
     * Mark voucher as reserved
     */
    public function markAsReserved()
    {
        self::$blCheckWasPerformed = true;
    }
}
