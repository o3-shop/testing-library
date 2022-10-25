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
namespace OxidEsales\TestingLibrary\Services\ShopObjectConstructor\Constructor;



/**
 * Class oxConfigCaller
 */
class oxConfigConstructor extends ObjectConstructor
{

    /**
     * Skip loading of config object, as it is already loaded
     *
     * @param string $objectId
     */
    public function load($objectId)
    {
    }

    /**
     * Sets class parameters
     *
     * @param array $classParams
     *
     * @return array
     */
    public function setClassParameters($classParams)
    {
        $values = array();
        foreach ($classParams as $sConfKey => $configParameters) {
            if (is_int($sConfKey)) {
                $values[$configParameters] = $this->getObject()->getConfigParam($configParameters);
            } else {
                $aFormedParams = $this->_formSaveConfigParameters($sConfKey, $configParameters);
                if ($aFormedParams) {
                    $this->callFunction("saveShopConfVar", $aFormedParams);
                }
            }
        }

        return $values;
    }

    /**
     * Returns created object to work with
     *
     * @param string $className
     *
     * @return \OxidEsales\Eshop\Core\Config
     */
    protected function _createObject($className)
    {
        return oxNew(\OxidEsales\Eshop\Core\Config::class);
    }

    /**
     * Forms parameters for saveShopConfVar function from given parameters
     *
     * @param string $configKey
     * @param array  $configParameters
     * @return array|bool
     */
    private function _formSaveConfigParameters($configKey, $configParameters)
    {
        $type = null;
        if (isset($configParameters['type'])) {
            $type = $configParameters['type'];
        }

        $value = null;
        if (isset($configParameters['value'])) {
            $value = $configParameters['value'];
        }

        $module = null;
        if (isset($configParameters['module'])) {
            $module = $configParameters['module'];
        }

        if (($type == "arr" || $type == 'aarr') && !is_array($value)) {
            $value = unserialize(htmlspecialchars_decode($value));
        }
        return !empty($type) ? array($type, $configKey, $value, null, $module) : false;
    }
}
