<?php

namespace Teapot;

abstract class Model
{
    protected $db;
    protected $request;

    protected $status = 'unknown';
    protected $reason = 'unknown';
    protected $state  = 501; # https://www.w3schools.com/tags/ref_httpmessages.asp

    private $dataType = 'json';
    private $data;

    public function __construct($dataType = 'json')
    {
        // setup the database connection
        $this->db = new db_connect();
    }
    public function request($request = null, $data = [])
    {

        // check to see if a request is being made
        if (!$request || !is_string($request)) {
            if (is_array($request)) {
                if (isset($request['request'])) {
                    // when the request is an array, attempt to interpret the array
                    $data    = $request;
                    $request = $request['request'];
                    unset($data['request']);
                }

            } else {
                $this->returnError('No request passed to the model.');
            }
        }
        $this->request = $request;
        // check for the method and return error if not found
        if (!method_exists($this, $request)) {
            $this->returnError('Request method does not exist.');
        }

        // load the request method
        $results = $this->{$this->request}($data);
        if ($results !== null) {
            $this->status = 'success';
            $this->reason = 'All data acquired successfully.';
            $this->state  = 200;
            $this->echoResults($results);
        } else {
            $this->echoResults('No results returned from Model', 204);
        }

    }

    protected function returnError($reason, $state = 501)
    {
        // set the status to an error state
        $this->status = 'error';
        $this->reason = $reason;
        $this->state  = $state;
        $this->echoResults();
    }

    protected function echoResults($results = [], $dataType = 'json')
    {
        // fix the results to an array if not array by default
        switch ($dataType) {
            default:
                echo json_encode([
                    'status'  => $this->status,
                    'reason'  => $this->reason,
                    'state'   => $this->state,
                    'results' => $results,
                ]);
        }
        exit;
    }

}
