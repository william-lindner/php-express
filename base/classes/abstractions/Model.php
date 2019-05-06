<?php

namespace Express\Abstractions;

use Express\PDO;

abstract class Model
{
    protected $db;

    private $data;

    public function __construct($data = [])
    {
        $this->db   = new PDO();
        $this->data = $data;
    }

}
