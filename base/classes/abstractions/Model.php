<?php

namespace Express\Abstractions;

use Express\PDO;

abstract class Model
{
    protected $db;

    private $data;

    public function __construct($data = [])
    {
        // @todo : setup new query class to hold queries as called
        // and store the results for faster returns
        $this->db   = new PDO();
        $this->data = $data;
    }

}
