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

use Exception;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestSuite;
use PHPUnit\TextUI\ResultPrinter;

class Printer implements ResultPrinter
{
    /** @var array */
    private $timeStats;

    /** @var bool */
    private $verbose;

    /** @var resource */
    private $out;

    public function __construct($out = null, bool $verbose = false)
    {
        $this->out = $out;
        $this->verbose = $verbose;
    }

    /**
     * @param string $buffer
     */
    public function write(string $buffer): void
    {
        if ((PHP_SAPI == 'cli')) {
            \fwrite(STDOUT, $buffer);
        } elseif ($this->out) {
            \fwrite($this->out, $buffer);
        } else {
            if (PHP_SAPI != 'cli' && PHP_SAPI != 'phpdbg') {
                $buffer = \htmlspecialchars($buffer, ENT_SUBSTITUTE);
            }

            print $buffer;
        }
    }

    /**
     * An error occurred.
     */
    public function addError(Test $test, Throwable $throwable, float $time): void
    {
        if ($this->verbose) {
            $this->write("        ERROR: '" . $throwable->getMessage() . "'\n" . $throwable->getTraceAsString());
        }
    }

    /**
     * A failure occurred.
     */
    public function addFailure(Test $test, AssertionFailedError $e, float $time): void
    {
        if ($this->verbose) {
            $this->write("        FAIL: '" . $e->getMessage() . "'\n" . $e->getTraceAsString());
        }
    }

    /**
     * A warning occurred.
     */
    public function addWarning(Test $test, Warning $e, float $time): void
    {
        if ($this->verbose) {
            $this->write("        WARNING: '" . $e->getMessage() . "'\n" . $e->getTraceAsString());
        }
    }

    /**
     * A test ended.
     */
    public function endTest(Test $test, float $time): void
    {
        if ($this->verbose) {
            $t = microtime(true) - $this->timeStats['startTime'];
            if ($this->timeStats['min'] > $t) {
                $this->timeStats['min'] = $t;
            }
            if ($this->timeStats['max'] < $t) {
                $this->timeStats['max'] = $t;
                $this->timeStats['slowest'] = $test->getName();
            }
            $this->timeStats['avg'] = ($t + $this->timeStats['avg'] * $this->timeStats['cnt']) / (++$this->timeStats['cnt']);
        }
    }

    /**
     * A test suite ended.
     */
    public function endTestSuite(TestSuite $suite): void
    {
        if ($this->verbose) {
            $this->write("\ntime stats: min {$this->timeStats['min']}, max {$this->timeStats['max']}, avg {$this->timeStats['avg']}, slowest test: {$this->timeStats['slowest']}|\n");
        }
    }

    /**
     * A test suite started.
     */
    public function startTestSuite(TestSuite $suite): void
    {
        if ($this->verbose) {
            $this->write("\n\n" . $suite->getName() . "\n");

            $this->timeStats = array('cnt' => 0, 'min' => 9999999, 'max' => 0, 'avg' => 0, 'startTime' => 0, 'slowest' => '_ERROR_');
        }
    }

    /**
     * A test started.
     */
    public function startTest(Test $test): void
    {
        if ($this->verbose) {
            $this->write("\n        " . $test->getName());

            $this->timeStats['startTime'] = microtime(true);
        }
    }
}