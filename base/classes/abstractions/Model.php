<?php

namespace Express\Abstractions;

use Express\Database\PDO;

abstract class Model
{
    protected $db;

    private $data;

    public function __construct($data = [])
    {
        $this->db   = new PDO();
        $this->data = $data;
    }

    public function query($name)
    {
        // dig into the queries app/queries/user/by-user-id
        // run the query and create an instance of whatever class called it
        $query = new Express\Database\Query('');
        return $query->get();
    }
}
