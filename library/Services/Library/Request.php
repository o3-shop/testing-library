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
namespace OxidEsales\TestingLibrary\Services\Library;

/**
 * Class used for uploading files in services.
 */
class Request
{
    /** @var array Request parameters */
    private $parameters = array();

    /**
     * Sets parameters to request
     *
     * @param array $parameters
     */
    public function __construct($parameters = null)
    {
        $this->parameters = $_REQUEST;
        if (!empty($parameters)) {
            $this->parameters = array_merge($this->parameters, $parameters);
        }
    }

    /**
     * Returns request parameter
     *
     * @param string $name
     * @param null   $default
     *
     * @return mixed
     */
    public function getParameter($name, $default = null)
    {
        return array_key_exists($name, $this->parameters) ? $this->parameters[$name] : $default;
    }

    /**
     * Returns uploaded file parameter
     *
     * @param string $name param name
     *
     * @return mixed
     */
    public function getUploadedFile($name)
    {
        $filePath = '';
        if (array_key_exists($name, $_FILES)) {
            $filePath = $_FILES[$name]['tmp_name'];
        } elseif (array_key_exists($name, $this->parameters)) {
            $filePath = substr($this->parameters[$name], 1);
        }

        return $filePath;
    }
}
