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

namespace OxidEsales\TestingLibrary;

class ObjectValidator
{
    /**
     * @var array
     */
    private $_aErrors = array();

    /**
     * @param      $sClass
     * @param      $aExpectedParams
     * @param null $sOxId
     * @return bool
     */
    public function validate($sClass, $aExpectedParams, $sOxId = null)
    {
        $aObjectParams = $this->_getObjectParameters($sClass, array_keys($aExpectedParams), $sOxId);

        $blResult = true;
        foreach ($aExpectedParams as $sKey => $sExpectedValue) {
            $sObjectValue = $aObjectParams[$sKey];
            if ($sExpectedValue !== $sObjectValue) {
                $this->_setError("'$sExpectedValue' != '$sObjectValue' on key '$sKey'");
                $blResult = false;
            }
        }

        return $blResult;
    }

    /**
     * Returns formed error message if parameters was not valid
     *
     * @return string
     */
    public function getErrorMessage()
    {
        $sMessage = '';
        $aErrors = $this->_getErrors();
        if (!empty($aErrors)) {
            $sMessage = "Expected and actual parameters do not match: \n";
            $sMessage .= implode("\n", $aErrors);
        }

        return $sMessage;
    }

    /**
     * Sets error message to error stack
     *
     * @param string $sMessage
     */
    protected function _setError($sMessage)
    {
        $this->_aErrors[] = $sMessage;
    }

    /**
     * Returns errors array
     *
     * @return array
     */
    protected function _getErrors()
    {
        return $this->_aErrors;
    }

    /**
     * Returns object parameters
     *
     * @param string $sClass
     * @param array  $aObjectParams
     * @param string $sOxId
     * @param string $sShopId
     *
     * @return mixed
     */
    protected function _getObjectParameters($sClass, $aObjectParams, $sOxId = null, $sShopId = null)
    {
        $oServiceCaller = new ServiceCaller();
        $oServiceCaller->setParameter('cl', $sClass);

        $sOxId = $sOxId ? $sOxId : 'lastInsertedId';
        $oServiceCaller->setParameter('oxid', $sOxId);
        $oServiceCaller->setParameter('classparams', $aObjectParams);

        return $oServiceCaller->callService('ShopObjectConstructor', $sShopId);
    }
}
