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
 * Helper class for \OxidEsales\Eshop\Core\Email
 * @deprecated since v4.0.0
 */
class oxEmailHelper extends \OxidEsales\Eshop\Core\Email
{
    /** @var bool Return value of any defined function. */
    public static $blRetValue = null;

    /** @var bool Whether email was sent to user. */
    public static $blSendToUserWasCalled = null;

    /** @var bool Whether email was sent to shop owner.  */
    public static $blSendToOwnerWasCalled = null;

    /** @var \OxidEsales\Eshop\Application\Model\Order User order used during email sending. */
    public static $oUserOrder = null;

    /** @var \OxidEsales\Eshop\Application\Model\Order Owner order used during email sending. */
    public static $oOwnerOrder = null;

    /**
     * Mocked method for testing.
     *
     * @param \OxidEsales\Eshop\Application\Model\Order $oOrder
     * @param string  $sSubject
     *
     * @return bool
     */
    public function sendOrderEmailToUser($oOrder, $sSubject = null)
    {
        self::$blSendToUserWasCalled = true;
        self::$oUserOrder = $oOrder;

        return self::$blRetValue;
    }

    /**
     * Mocked method for testing.
     *
     * @param \OxidEsales\Eshop\Application\Model\Order $order
     * @param string    $subject
     *
     */
    public function sendOrderEmailToOwner($order, $subject = null)
    {
        self::$blSendToOwnerWasCalled = true;
        self::$oOwnerOrder = $order;

        return null;
    }

    /**
     * Mocked method for testing.
     *
     * @param \OxidEsales\Eshop\Application\Model\User $oUser
     * @param string $sSubject
     *
     * @return bool
     */
    public function sendNewsletterDBOptInMail($oUser, $sSubject = null)
    {
        return self::$blRetValue;
    }
}
