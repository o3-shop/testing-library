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

use PHPUnit\Framework\MockObject\Invocation;

/**
 * Class for creating stub objects.
 */
class MockStubFunc implements \PHPUnit\Framework\MockObject\Stub\Stub
{
    /** @var string */
    private $_func;

    /**
     * Constructor
     *
     * @param string $sFunc
     */
    public function __construct($sFunc)
    {
        $this->_func = $sFunc;
    }

    /**
     * Fakes the processing of the invocation $invocation by returning a
     * specific value.
     *
     * @param Invocation $invocation
     * The invocation which was mocked and matched by the current method
     * and argument matchers.
     *
     * @return mixed
     */
    public function invoke(Invocation $invocation)
    {
        if (is_string($this->_func) && preg_match('/^\{.+\}$/', $this->_func)) {
            $args = $invocation->getParameters();
            $_this = $invocation->getObject();

            return eval($this->_func);
        } else {
            return call_user_func_array($this->_func, $invocation->getParameters());
        }
    }

    /**
     * Returns user called function.
     *
     * @return string
     */
    public function toString(): string
    {
        return 'call user-specified function ' . $this->_func;
    }
}
