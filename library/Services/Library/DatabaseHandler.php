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

use Exception;
use PDO;
use PDOStatement;
use PDOException;
use OxidEsales\Eshop\Core\ConfigFile;

/**
 * Simple database connector.
 */
class DatabaseHandler
{
    /** @var ConfigFile */
    private $configFile;

    /** @var PDO Database connection. */
    private $dbConnection;

    /** @var DatabaseDefaultsFileGenerator */
    private $databaseDefaultsFileGenerator;

    /**
     * Initiates class dependencies.
     *
     * @param ConfigFile $configFile
     *
     * @throws Exception
     */
    public function __construct($configFile)
    {
        $this->configFile = $configFile;
        $this->databaseDefaultsFileGenerator = new DatabaseDefaultsFileGenerator($configFile);
        if (!extension_loaded('pdo_mysql')) {
            throw new \Exception("the php pdo_mysql extension is not installed!\n");
        }

        $dsn = 'mysql' .
               ':host=' . $this->getDbHost() .
               (empty($this->getDbPort()) ? '' : ';port=' . $this->getDbPort());

        try {
            $this->dbConnection = new PDO(
                $dsn,
                $this->getDbUser(),
                $this->getDbPassword(),
                array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
            );
        } catch (\PDOException $exception) {
            throw new \Exception("Could not connect to '{$this->getDbHost()}' with user '{$this->getDbUser()}'\n");
        }
    }

    /**
     * Execute sql statements from sql file
     *
     * @param string $sqlFile     SQL File name to import.
     * @param string $charsetMode Charset of imported file. Will use shop charset mode if not set.
     *
     * @throws Exception
     */
    public function import($sqlFile, $charsetMode = null)
    {
        if (!file_exists($sqlFile)) {
            throw new Exception("File '$sqlFile' was not found.");
        }

        $credentialsFile = $this->databaseDefaultsFileGenerator->generate();
        $charsetMode = $charsetMode ? $charsetMode : $this->getCharsetMode();
        $command = 'mysql --defaults-file=' . $credentialsFile;
        $command .= ' --default-character-set=' . $charsetMode;
        $command .= ' ' .escapeshellarg($this->getDbName());
        $command .= ' < ' . escapeshellarg($sqlFile);
        $this->executeCommand($command);
        unlink($credentialsFile);
    }

    /**
     * @param string $sqlFile
     * @param array  $tables
     */
    public function export($sqlFile, $tables)
    {
        $credentialsFile = $this->databaseDefaultsFileGenerator->generate();
        $command = 'mysqldump --defaults-file=' . $credentialsFile;
        if (!empty($tables)) {
            array_map('escapeshellarg', $tables);
            $tables = ' ' . implode($tables);
        }
        $command .= ' ' . escapeshellarg($this->getDbName()) . $tables;
        $command .= ' > ' . escapeshellarg($sqlFile);
        $this->executeCommand($command);
        unlink($credentialsFile);
    }

    /**
     * Executes query on database.
     *
     * @param string $sql Sql query to execute.
     *
     * @return PDOStatement|false
     */
    public function query($sql)
    {
        $this->useConfiguredDatabase();
        $return = $this->getDbConnection()->query($sql);
        $this->checkForDatabaseError($sql, 'query');
        return $return;
    }

    /**
     * This function is intended for write access to the database like INSERT, UPDATE
     *
     * @param string $sql Sql query to execute.
     *
     * @return int
     */
    public function exec($sql)
    {
        $this->useConfiguredDatabase();
        $success = $this->getDbConnection()->exec($sql);
        $this->checkForDatabaseError($sql, 'exec');
        return $success;
    }

    /**
     * Executes sql query. Returns query execution resource object
     *
     * @param string $sql query to execute
     *
     * @throws Exception exception is thrown if error occured during sql execution
     *
     * @return PDOStatement|false|int
     */
    public function execSql($sql)
    {
        try {
            list ($statement) = explode(" ", ltrim($sql));
            if (in_array(strtoupper($statement), array('SELECT', 'SHOW'))) {
                $oStatement = $this->query($sql);
            } else {
                return $this->exec($sql);
            }

            return $oStatement;
        } catch (PDOException $e) {
            throw new Exception("Could not execute sql: " . $sql);
        }
    }

    /**
     * The database if not chosen when the connection is made because the database can be e.g. dropped afterwards
     * and then the connection gets lost.
     *
     * @throws Exception
     */
    protected function useConfiguredDatabase()
    {
        try {
            $this->getDbConnection()->exec('USE `' . $this->getDbName() . '`');
        } catch (Exception $e) {
            throw new Exception("Could not connect to database " . $this->getDbName());
        }
    }

    /**
     * @param string $value
     * @return string
     */
    public function escape($value)
    {
        return $this->getDbConnection()->quote($value);
    }

    /**
     * Returns charset mode
     *
     * @return string
     */
    public function getCharsetMode()
    {
        return 'utf8';
    }

    /**
     * @return string
     */
    public function getDbName()
    {
        return $this->configFile->dbName;
    }

    /**
     * @return string
     */
    public function getDbUser()
    {
        return $this->configFile->dbUser;
    }

    /**
     * @return string
     */
    public function getDbPassword()
    {
        return $this->configFile->dbPwd;
    }

    /**
     * @return string
     */
    public function getDbHost()
    {
        return $this->configFile->dbHost;
    }

    /**
     * @return string
     */
    public function getDbPort()
    {
        return $this->configFile->dbPort;
    }

    /**
     * Returns database resource
     *
     * @return PDO
     */
    public function getDbConnection()
    {
        return $this->dbConnection;
    }

    /**
     * Execute shell command
     *
     * @param string $command
     *
     * @throws Exception
     */
    protected function executeCommand($command)
    {

        try {
            CliExecutor::executeCommand($command);
        } catch (Exception $e) {
            exit($e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL);
        }
    }

    /**
     * Check for error code in database connection.
     *
     * @param string $query
     * @param string $callingFunctionName
     *
     * @throws Exception
     */
    protected function checkForDatabaseError($query, $callingFunctionName)
    {
        $dbCon = $this->getDbConnection();
        if (is_a($dbCon, 'PDO') && ('00000' !== $dbCon->errorCode())) {
            $errorInfo = $dbCon->errorInfo();
            throw new Exception('PDO error code: ' . $dbCon->errorCode() . ' in function ' . $callingFunctionName . ' -- ' . $errorInfo[2] . ' -- ' . $query);
        }
    }
}
