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
 * Class oxTestCacheConnector
 */
class oxTestCacheConnector implements \OxidEsales\Eshop\Application\Model\Contract\CacheConnectorInterface
{
    /** @var array Cached items. */
    public $aCache = array();

    /**
     * Returns whether this cache connector is available.
     *
     * @return bool
     */
    public static function isAvailable()
    {
        return true;
    }

    /**
     * @throws Exception
     */
    public function __construct()
    {
        if (!self::isAvailable()) {
            throw new Exception('CONNECTOR_NOT_AVAILABLE');
        }
    }

    /**
     * Adds item to cache.
     *
     * @param array|string $mKey
     * @param mixed        $mValue
     * @param int          $iTTL
     *
     */
    public function set($mKey, $mValue = null, $iTTL = null)
    {
        if (is_array($mKey)) {
            $this->aCache = array_merge($this->aCache, $mKey);
        } else {
            $this->aCache[$mKey] = $mValue;
        }
    }

    /**
     * Returns cached item value.
     *
     * @param array|string $mKey
     * @return array
     */
    public function get($mKey)
    {
        if (is_array($mKey)) {
            return array_intersect_key($this->aCache, array_flip($mKey));
        } else {
            return $this->aCache[$mKey];
        }
    }

    /**
     * Invalidates item's cache.
     *
     * @param array|string $mKey
     *
     */
    public function invalidate($mKey)
    {
        if (is_array($mKey)) {
            $this->aCache = array_diff_key($this->aCache, array_flip($mKey));
        } else {
            $this->aCache[$mKey] = null;
        }

    }

    /**
     * Clears cache
     */
    public function flush()
    {
        $this->aCache = array();
    }
}
