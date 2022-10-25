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
namespace OxidEsales\TestingLibrary\Services\SubShopHandler;

use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\TestingLibrary\Services\Library\Request;
use OxidEsales\TestingLibrary\Services\Library\ServiceConfig;
use OxidEsales\TestingLibrary\Services\Library\ShopServiceInterface;

/**
 * Assigns items to subshop
 */
class SubShopHandler implements ShopServiceInterface
{
    /**
     * @param ServiceConfig $config
     */
    public function __construct($config) {}

    /**
     * Assigns element to subshop
     *
     * @param Request $request
     */
    public function init($request)
    {
        $sElementTable = $request->getParameter("elementtable");
        $sShopId = $request->getParameter("shopid");
        $sParentShopId = $request->getParameter("parentshopid");
        $sElementId = $request->getParameter("elementid");
        if ($sElementId) {
            $this->assignElementToSubShop($sElementTable, $sShopId, $sElementId);
        } else {
            $this->assignAllElementsToSubShop($sElementTable, $sShopId, $sParentShopId);
        }
    }

    /**
     * Assigns element to subshop
     *
     * @param string  $sElementTable Name of element table
     * @param integer $sShopId       Subshop id
     * @param integer $sElementId    Element id
     */
    public function assignElementToSubShop($sElementTable, $sShopId, $sElementId)
    {
        /** @var BaseModel $oBase */
        $oBase = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
        $oBase->init($sElementTable);
        if ($oBase->load($sElementId)) {
            /** @var \OxidEsales\Eshop\Core\Element2ShopRelations $oElement2ShopRelations */
            $oElement2ShopRelations = oxNew(\OxidEsales\Eshop\Core\Element2ShopRelations::class, $sElementTable);
            $oElement2ShopRelations->setShopIds($sShopId);
            $oElement2ShopRelations->addToShop($oBase->getId());
        }
    }

    /**
     * Assigns element to subshop
     *
     * @param string  $sElementTable Name of element table
     * @param integer $sShopId       Subshop id
     * @param integer $sParentShopId Parent subshop id
     */
    public function assignAllElementsToSubShop($sElementTable, $sShopId, $sParentShopId = 1)
    {
        /** @var \OxidEsales\Eshop\Core\Element2ShopRelations $oElement2ShopRelations */
        $oElement2ShopRelations = oxNew(\OxidEsales\Eshop\Core\Element2ShopRelations::class, $sElementTable);
        $oElement2ShopRelations->setShopIds($sShopId);
        $oElement2ShopRelations->inheritFromShop($sParentShopId);
    }
}
