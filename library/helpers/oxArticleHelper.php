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
 * Helper class for \OxidEsales\Eshop\Application\Model\Article
 * @deprecated since v4.0.0
 */
class oxArticleHelper extends \OxidEsales\Eshop\Application\Model\Article
{
    /**
     * Constructor
     *
     * @param array $params Parameters
     */
    public function __construct($params = null)
    {
        $this->cleanup();
        parent::__construct($params);
    }

    /**
     * Clean oxArticle static variables.
     */
    public static function cleanup()
    {
        self::resetArticleCategories();
        self::resetCache();
        self::resetAmountPrice();
    }

    /**
     * Get private field value.
     *
     * @param string $name Field name
     *
     * @return mixed
     */
    public function getVar($name)
    {
        return $this->{'_' . $name};
    }

    /**
     * Set private field value.
     *
     * @param string $name  Field name
     * @param string $value Field value
     */
    public function setVar($name, $value)
    {
        $this->{'_' . $name} = $value;
    }

    /**
     * Reset cached private variable values.
     */
    public static function resetCache()
    {
        parent::$_aArticleVendors = array();
        parent::$_aArticleManufacturers = array();
        parent::$_aLoadedParents = null;
        parent::$_aSelList = null;
    }

    /**
     * Clean private variable values.
     */
    public static function resetArticleCategories()
    {
        parent::$_aArticleCats = array();
    }

    /**
     * Reset cached private variable values.
     */
    public static function resetAmountPrice()
    {
        parent::$_blHasAmountPrice = null;
    }
}
