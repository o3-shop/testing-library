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

use Iterator;

/**
 * Class oxConfigCaller
 */
class oxListConstructor extends ObjectConstructor
{
    /**
     * Skip loading of config object, as it is already loaded
     *
     * @param string $objectId
     */
    public function load($objectId)
    {
        $this->getObject()->init($objectId, $objectId);
    }

    /**
     * Calls object function with given parameters
     *
     * @param string $functionName
     * @param array $parameters
     * @return mixed
     */
    public function callFunction($functionName, $parameters)
    {
        if ($functionName == 'getList') {
            $oObject = $this->getObject();
            $mResponse = $this->_formArrayFromList($oObject->getList());
        } else {
            $mResponse = parent::callFunction($functionName, $parameters);
        }

        return $mResponse;
    }

    /**
     * Returns formed array with data from given list
     *
     * @param \OxidEsales\Eshop\Core\Model\ListModel|Iterator $oList
     * @return array
     */
    protected function _formArrayFromList($oList)
    {
        $aData = array();
        foreach ($oList as $sKey => $object) {
            $aData[$sKey] = $this->_getObjectFieldValues($object);
        }

        return $aData;
    }

    /**
     * Returns object field values
     *
     * @param \OxidEsales\Eshop\Core\Model\BaseModel|object $object
     *
     * @return array
     */
    protected function _getObjectFieldValues($object)
    {
        $result = array();
        $fields = $object->getFieldNames();
        $tableName = $object->getCoreTableName();
        foreach ($fields as $field) {
            $fieldName = $tableName.'__'.$field;
            $result[$field] = $object->$fieldName->value;
        }

        return $result;
    }
}
