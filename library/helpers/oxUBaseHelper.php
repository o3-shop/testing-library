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
 * Helper class for \OxidEsales\Eshop\Application\Controller\FrontendController
 * @deprecated since v4.0.0
 */
class oxUBaseHelper extends \OxidEsales\Eshop\Application\Controller\FrontendController
{

    /** @var bool Was init function called. */
    public $initWasCalled = false;

    /** @var bool Was parent class called. */
    public $setParentWasCalled = false;

    /** @var bool Whether action was set. */
    public $setThisActionWasCalled = false;

    /**
     * Calls self::_processRequest(), initializes components which needs to
     * be loaded, sets current list type, calls parent::init()
     */
    public function init()
    {
        $this->initWasCalled = true;
    }

    /**
     * Cleans classes static variables.
     */
    public static function cleanup()
    {
        self::resetComponentNames();
    }

    /**
     * Sets class parent.
     *
     * @param null $oParam
     */
    public function setParent($oParam = null)
    {
        $this->setParentWasCalled = true;
    }

    /**
     * Sets action.
     *
     * @param null $oParam
     */
    public function setThisAction($oParam = null)
    {
        $this->setThisActionWasCalled = true;
    }

    /**
     * Resets collected component names.
     */
    public static function resetComponentNames()
    {
        parent::$_aCollectedComponentNames = null;
    }
}
