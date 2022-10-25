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
namespace OxidEsales\TestingLibrary\Services\ShopObjectConstructor;

use Exception;
use OxidEsales\TestingLibrary\Services\Library\Request;
use OxidEsales\TestingLibrary\Services\Library\ServiceConfig;
use OxidEsales\TestingLibrary\Services\Library\ShopServiceInterface;
use OxidEsales\TestingLibrary\Services\ShopObjectConstructor\Constructor\ConstructorFactory;


/**
 * Shop constructor class for modifying shop environment during testing
 * Class ShopConstructor
 */
class ShopObjectConstructor implements ShopServiceInterface
{
    /** @var ServiceConfig */
    private $serviceConfig;

    /**
     * @param ServiceConfig $config
     */
    public function __construct($config)
    {
        $this->serviceConfig = $config;
    }

    /**
     * Loads object, sets class parameters and calls function with parameters.
     * classParams can act two ways - if array('param' => 'value') is given, it sets the values to given keys
     * if array('param', 'param') is passed, values of these params are returned.
     * classParams are only returned if no function is called. Otherwise function return value is returned.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function init($request)
    {
        if (!is_null($request->getParameter('shp'))) {
            $this->setActiveShop($request->getParameter('shp'));
        }
        if (!is_null($request->getParameter('lang'))) {
            $this->setActiveLanguage($request->getParameter('lang'));
        }

        $oConstructorFactory = new ConstructorFactory();
        $oConstructor = $oConstructorFactory->getConstructor($request->getParameter("cl"));

        $oConstructor->load($request->getParameter("oxid"));

        $mResult = '';
        if ($request->getParameter('classparams')) {
            $mResult = $oConstructor->setClassParameters($request->getParameter('classparams'));
        }

        if ($request->getParameter('fnc')) {
            $mResult = $oConstructor->callFunction($request->getParameter('fnc'), $request->getParameter('functionparams'));
        }

        return $mResult;
    }

    /**
     * @return ServiceConfig
     */
    protected function getServiceConfig()
    {
        return $this->serviceConfig;
    }

    /**
     * Switches active shop
     *
     * @param string $shopId
     */
    protected function setActiveShop($shopId)
    {
        if ($shopId && $this->getServiceConfig()->getShopEdition() == 'EE') {
            \OxidEsales\Eshop\Core\Registry::getConfig()->setShopId($shopId);
        }
    }

    /**
     * Switches active language
     *
     * @param string $language
     *
     * @throws Exception
     */
    protected function setActiveLanguage($language)
    {
        $languages = \OxidEsales\Eshop\Core\Registry::getLang()->getLanguageIds();
        $languageId = array_search($language, $languages);
        if ($languageId === false) {
            throw new Exception("Language $language was not found or is not active in shop");
        }
        \OxidEsales\Eshop\Core\Registry::getLang()->setBaseLanguage($languageId);
    }
}
