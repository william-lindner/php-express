<?php

namespace Teapot;

DEFINE('PDO_TIMEOUT_REPEAT', true);
DEFINE('PDO_TIMEOUT_IGNORE', false);
// DEFINE('CONFIGURATION_PATH', $_SERVER['DOCUMENT_ROOT'] . '/../../configuration'); # enable if doing isolated testing

class PDOdbc extends PDO
{
    /*
    Initial settings are for locating the .ini files for your db connections.
    You may setup as many as you like so long as the name of the .ini file begins with the
    same string as you call when instantiating this class.

    The .ini file should be formatted as below:
    [database]
    hostname = HOSTNAME
    username = "USERNAME"
    password = "PASSWORD"
    dbname = "DATABASE"
    port = PORT_NUM

     */
    const INI_PATH = CONFIGURATION_PATH . '/database/';
    const INI_EXT  = '.ini';
    /*
    Enabling multi line queries is not typically recommended. Disable this when you have less experienced
    developers. More experienced developers will work around the issues in other ways.
     */
    const ENABLE_MULTI_LINE_QUERIES = true;
    const ENABLE_QUERY              = false;
    const CHARSET                   = 'UTF8';

    public $status     = 'success';
    public $error      = null;
    public $error_info = null;
    public $error_code = null;
    public $found_rows = 0;
    public $last_id    = null;

    private $pdo;
    private $iterations = 0;

    public function __construct($dbname = 'general')
    {
        $dbname = 'general'; // tmp code to fix anomalies - static general connection
        return self::pdo_connect($dbname);
    }

    public function pdo_connect($dbname)
    {
        // load the default db file as configured by the server
        if (defined('DATABASE_FILE')) {
            $dbname = DATABASE_FILE;
        }
        // add the .ini extension if not present
        if (strpos($dbname, '.ini') === false) {
            $dbname .= self::INI_EXT;
        }
        // Load configuration as an array. Use the actual location of your configuration file
        if (!$config = @parse_ini_file(self::INI_PATH . $dbname)) {
            throw new \Exception('Unable to locate designated ini at established path.', 443);
        }

        //try to connect to db by calling the parent constructor in PDO
        return parent::__construct(
            'mysql:dbname=' . $config['dbname'] . ';host=' . $config['hostname'],
            $config['username'],
            $config['password'],
            [
                self::MYSQL_ATTR_INIT_COMMAND     => 'SET NAMES \'UTF8\'',
                self::MYSQL_ATTR_MULTI_STATEMENTS => self::ENABLE_MULTI_LINE_QUERIES,
                self::MYSQL_ATTR_FOUND_ROWS       => true,
                self::ATTR_DEFAULT_FETCH_MODE     => self::FETCH_ASSOC,
                self::ATTR_DRIVER_NAME            => 'mysql',
                self::ATTR_ERRMODE                => self::ERRMODE_EXCEPTION,
            ]);
    }

    //Prevent the use of PDO::query() by throwing an exception. Internally use parent::query()
    public function query($sql)
    {
        if (self::ENABLE_QUERY) {
            return parent::query($sql);
        } else {
            throw new \Exception('Direct query function calls are not permitted. Please use request().', 500);
        }
    }

    //prepared statement short hand with integrity checks
    public function request($query, $params = [], $repeat_on_timeout = PDO_TIMEOUT_IGNORE)
    {
        $bools       = [true, false, 'true', 'false'];
        $bind_params = false;

        $query       = trim($query);
        $starts_with = strtolower(substr($query, 0, 7));

        $select = (strpos($starts_with, 'select') !== false);
        $call   = (strpos($starts_with, 'call') !== false);
        $delete = (strpos($starts_with, 'delete') !== false);
        $insert = (strpos($starts_with, 'insert') !== false);
        $update = (strpos($starts_with, 'update') !== false);

        //convert the content into arrays if they did not start that way
        if (!is_array($params)) {
            $params = [$params];
        }

        //return in error when the bind params do not match the number of params
        if (($bind_params = (strpos($query, '?') !== false)) || $params) {
            if ((substr_count($query, '?') !== count($params)) || (substr_count($query, '?') > 0 && !is_array($params))) {
                throw new \PDOException('Binded parameters and variable count mismatch. Received ' . substr_count($query, '?') . ' bind params and ' . count($params) . ' params');
            }
        }

        /*
        False will evaluate to NULL, and true/false boolean operators do not insert / update into the db properly.
        This code resolves this issue by replacing them with int equivalents.
         */
        if ($bind_params && $params) {
            if (array_intersect($params, $bools)) {
                $new_params = array_map(array($this, 'fix_bool'), $params);
                $params     = $new_params;
            }
        }
        /*
        When not using bind parameter prepared sql stmts parse the content for null values
        reconstruct the sql stmt without the null parameters allowing the db to handle null exceptions

         */
        if ($bind_params) {
            if (in_array('null', $params, true) || in_array('NULL', $params, true)) {
                $params = array_map(function ($v) {
                    if (strtolower($v) === 'null') {
                        return null;
                    } else {
                        return $v;
                    }
                }, $params);
            }
        }

        $return = true;
        $epstmt = $this->prepare($query);
        // check for any errors with the statement
        $error            = $this->error_info            = $epstmt->errorInfo();
        $this->error      = $error[2];
        $this->error_code = $error[1];
        if ($error[1]) {
            throw new \PDOException($error[2], $error[1]);
        }

        // bind the parameters passed
        if (!empty($params)) {
            for ($i = 1; $i < count($params) + 1; $i++) {
                $epstmt->bindValue($i, $params[$i - 1]);
            }
        }

        execution_marker:
        $epstmt->execute();
        $impact = &$this->found_rows;
        $impact = $epstmt->rowCount();
        if (!$impact && !$call && !$select && !$delete) {
            $return = false;
        }

        if ($select) {
            $return = $epstmt->fetchAll() ?: false;
        }
        if ($call) {
            $return = $epstmt->fetchAll() ?: true;
        }

        $this->last_id = $this->lastInsertId();
        $return        = $this->last_id ?: $return;

        if (!$return && PDO_TIMEOUT_REPEAT === $repeat_on_timeout && $this->iterations < 3) {
            $this->iterations++;
            Server::writeLog('Query failed and had to be reran. ' . $query);
            usleep(200);
            goto execution_marker;
        } else {
            $this->iterations = 0;
        }

        return $return;
    }

    //used to fix false/true issues with prepared statements in PDO
    private function fix_bool($v)
    {
        if ('true' === $v) {
            return 1;
        }

        if ('false' === $v) {
            return 0;
        }

        if (is_bool($v)) {
            $v = ($v) ? 1 : 0;
        }

        return $v;
    }

    public function schwarzenegger()
    {
        return "I am here to pump you up.";
    }
} //END CLASS
