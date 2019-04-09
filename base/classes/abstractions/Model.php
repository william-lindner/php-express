<?php

namespace Express\Abstractions;

use Express\PDO;

abstract class Model
{
    protected $db;

    private $data;

    public function __construct()
    {
        $this->db = new PDO();
    }

}
