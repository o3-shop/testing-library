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
 * Helper class for \OxidEsales\Eshop\Core\Utils
 * @deprecated since v4.0.0
 */
class oxUtilsHelper extends \OxidEsales\Eshop\Core\Utils
{
    /** @var null Redirect url. */
    public static $sRedirectUrl = null;

    /** @var bool Should SEO engine be active during testing. */
    public static $sSeoIsActive = false;

    /** @var bool Should shop act as a search engine during testing. */
    public static $blIsSearchEngine = false;

    /**
     * Rewrites parent::redirect method.
     *
     * @param string $sUrl
     * @param bool   $blAddRedirectParam
     * @param int    $iHeaderCode
     *
     */
    public function redirect($sUrl, $blAddRedirectParam = true, $iHeaderCode = 301)
    {
        self::$sRedirectUrl = $sUrl;
    }

    /**
     * Rewrites parent::seoIsActive method.
     *
     * @param bool $blReset
     * @param null $sShopId
     * @param null $iActLang
     *
     * @return bool
     */
    public function seoIsActive($blReset = false, $sShopId = null, $iActLang = null)
    {
        return self::$sSeoIsActive;
    }

    /**
     * Rewrites parent::isSearchEngine method.
     *
     * @param bool $blReset
     * @param null $sShopId
     * @param null $iActLang
     * @return bool
     */
    public function isSearchEngine($blReset = false, $sShopId = null, $iActLang = null)
    {
        return self::$blIsSearchEngine;
    }
}
