<?php

namespace Express;

/**
 * While this PDO wrapper has a number of utility functions do keep in mind it
 * is an extension of the standard PDO library, so you will mostly have available
 * the core functions from that origin class. Use with caution.
 *
 */
class PDO extends \PDO
{
    const ENABLE_MULTI_LINE_QUERIES = false;

    public $error      = [];
    public $found_rows = 0;
    public $last_id    = null;

    /**
     * Sets up the PDO class with the Express environment configuration.
     * @return static
     */
    public function __construct()
    {
        $setting = config('database');

        /**
         * There is a bit of PHP trickery here in that despite the parent class
         * being returned the instance will have access to the functions in this class as well.
         */
        return parent::__construct(
            "mysql:dbname={$setting['database']};host={$setting['hostname']}",
            $setting['username'],
            $setting['password'],
            [
                static::MYSQL_ATTR_INIT_COMMAND     => "SET NAMES '{$setting['charset']}'",
                static::MYSQL_ATTR_MULTI_STATEMENTS => static::ENABLE_MULTI_LINE_QUERIES,
                static::MYSQL_ATTR_FOUND_ROWS       => true,
                static::ATTR_DEFAULT_FETCH_MODE     => static::FETCH_ASSOC,
                static::ATTR_DRIVER_NAME            => 'mysql',
                static::ATTR_ERRMODE                => static::ERRMODE_EXCEPTION,
            ]
        );
    }

    /**
     * Prevents the use of PDO::query() by throwing an exception.
     * Internally use parent::query()
     * @param  string $sql
     * @return bool
     */
    public function query(string $query)
    {
        throw new \Exception('Query function within Express\PDO is deprecated.', 400);
    }

    /**
     * SELECT content from a mysql database using prepared statement.
     *
     * @return array
     */
    public function select(string $query, $params = [], $fetchOptions = null)
    {
        $stmt = $this->run($query, $params, 'select');
        return $stmt->fetchAll($fetchOptions) ?: [];
    }

    /**
     * INSERT content from a mysql database using prepared statement.
     *
     * @return int|bool
     */
    public function insert(string $query, $params = [])
    {
        $this->run($query, $params, 'insert');
        return $this->last_id = $this->lastInsertId() ?: $this->found_rows;
    }

    /**
     * DELETE content from a mysql database using prepared statement.
     *
     * @return int|bool
     */
    public function delete(string $query, $params = [])
    {
        $this->run($query, $params, 'delete');
        return $this->last_id = $this->lastInsertId();
    }

    /**
     * UPDATE content from a mysql database using prepared statement.
     */
    public function update(string $query, $params = [])
    {
        $this->run($query, $params, 'update');
        return $this->found_rows;
    }

    /**
     * Executes a call to a procedure in SQL.
     *
     * @return array|bool
     */
    public function call(string $query, $params = [])
    {
        $stmt = $this->run($query, $params, 'call');
        return $stmt->fetchAll() ?: true;
    }

    /**
     * Executes a show request in SQL.
     *
     * @return array
     */
    public function show(string $query, $params = [])
    {
        $stmt = $this->run($query, $params, 'show');
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Returns the options for an enumerated column.
     * Warning: Enumerated columns are to be used sparingly.
     *
     * @return array
     */
    public function enum(string $table, string $column)
    {
        $table = $this->stripSemicolon($table);

        $row = $this->show(
            "SHOW COLUMNS FROM $table LIKE ?",
            $column
        );

        if (!$row) {
            return $row;
        }

        $enum = str_replace('\'\'', '\'', $row[0]['Type']);
        preg_match('/enum\(\'(.*)\'\)$/', $enum, $data);

        return explode('\',\'', $data[1]);
    }

    /**
     * Determines the SQL Query type by parsing the query.
     *
     * @param  string $query
     * @return string
     */
    protected function queryType(string $query)
    {
        $query       = trim($query);
        $starts_with = strtolower(substr($query, 0, 6));

        return explode(' ', $starts_with)[0] ?? '';
    }

    /**
     * Ensures the type is what is expected by the function call.
     *
     * @return void
     */
    protected function verifyType(string $type, string $expectation)
    {
        $expectation = strtolower($expectation);

        if ($type !== $expectation) {
            throw new \PDOException(
                "An invalid request was made. Expected '{$expectation}' and made '{$type}.'",
                400
            );
        }
    }

    /**
     * Verifies the parameters are ready for usage.
     *
     * @return array
     */
    protected function sanitizeParams(string $query, $params = [])
    {
        $params     = is_array($params) ? $params : [$params];
        $bindCount  = substr_count($query, '?');
        $paramCount = count($params);

        if (!(boolval($bindCount) || !empty($params))) {
            return [];
        }

        if ($bindCount !== $paramCount) {
            throw new \PDOException(
                "Parameter mismatch! Received {$bindCount} bind params and {$paramCount} params",
                400
            );
        }
        $params = $this->castBooleanParams($params);
        $params = $this->castNullParams($params);

        return $params;
    }

    /**
     * Resolves conflicts with coersion in SQL when interpretting false and null
     *
     * @return array
     */
    protected function castBooleanParams(array $params)
    {
        if (empty($params)) {
            return;
        }

        $truthiness = [true, 'true', 'TRUE', '1', 1];
        $falsiness  = [false, 'false', 'FALSE', '0', 0];

        $boolsToFix = array_merge($truthiness, $falsiness);
        $fixableSet = array_intersect($params, $boolsToFix);

        foreach ($fixableSet as $key => $value) {
            if ($value === null || is_int($value) || $value === '') {
                continue;
            }

            $params[$key] = (int) in_array($value, $truthiness, true);
        }

        return $params;
    }

    /**
     * Resolves conflicts with the string version of NULL being entered into string columns.
     *
     * @return array
     */
    protected function castNullParams(array $params)
    {
        $nullsToFix = ['null', 'NULL'];
        $fixableSet = array_intersect($params, $nullsToFix);

        foreach ($fixableSet as $key => $value) {
            $params[$key] = null;
        }

        return $params;
    }

    /**
     * Checks a prepared statement instance for any problems and assigns it to object.
     *
     * @return void
     */
    protected function checkErrors(\PDOStatement $stmt)
    {
        $error = $stmt->errorInfo();

        if ($error[1]) {
            throw new \PDOException($error[2], $error[1]);
        }
    }

    /**
     * Executes the SQL statement using prepared synatax.
     *
     * @return \PDOStatement
     */
    protected function run(string $query, $params = [], string $type)
    {
        $this->reset();
        $this->verifyType($this->queryType($query), $type);
        $params = $this->sanitizeParams($query, $params);

        $stmt = $this->prepare($query);
        $this->checkErrors($stmt);

        $stmt->execute($params);
        $this->found_rows = $stmt->rowCount();
        return $stmt;
    }

    /**
     * Sets the default values back to object attributes
     *
     * @return void
     */
    protected function reset()
    {
        $this->error      = [];
        $this->found_rows = 0;
        $this->last_id    = null;
    }

    /**
     * Catch all types of requests and use appropriate function.
     *
     * @return array|bool
     */
    public function request(string $query, $params = [])
    {
        $functionName = $this->queryType($query);
        if (!method_exists($this, $functionName)) {
            throw new \Exception('Unable to identify the query type to run request.', 400);
        }
        return $this->{$functionName}($query, $params);
    }

    /**
     * Utility function to strip semicolons from the queries.
     *
     * @return string
     */
    public function stripSemicolon(string $string)
    {
        return preg_replace('/;+/', ';', $string);
    }

    /**
     * Calls upon the forces of Arnold to Hey, girl your muscals.
     *
     * @return void
     */
    public function schwarzenegger()
    {
        return 'I am here to pump you up.';
    }
} //END CLASS
