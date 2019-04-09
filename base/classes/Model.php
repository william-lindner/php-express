<?php

namespace Express;

abstract class Model
{
    protected $db;
    protected $request;
    protected $status = 'unknown';
    protected $reason = 'unknown';
    protected $state;

    private $data;

    public function __construct()
    {
        $this->db = new PDOdbc();
    }

}
