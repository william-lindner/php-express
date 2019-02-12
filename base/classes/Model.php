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

    /**
     *
     */
    public function request($request = null, $data = [])
    {
        $this->state = 501;

        // Ajax commonly passes information as an array, so this was added to handle it.
        if (is_array($request)) {
            if (isset($request['request'])) {
                $data    = $request;
                $request = $request['request'];
                unset($data['request']);
            }
        }

        if (!$request || !is_string($request)) {
            $this->returnError('No request passed to the model.');
        }
        if (!method_exists($this, $request)) {
            $this->returnError('Request method does not exist.');
        }

        $this->request = $request;
        $results       = $this->{$this->request}($data);
        if ($results !== null) {
            $this->status = 'success';
            $this->reason = 'All data acquired successfully.';
            $this->state  = 200;
            $this->echoResults($results);

        } else {
            $this->returnError('No results returned from Model', 204);
        }
    }

    protected function returnError($reason, $state = 501)
    {
        $this->status = 'error';
        $this->reason = $reason;
        $this->state  = $state;
        $this->echoResults();
    }

    protected function echoResults($results = [])
    {
        $this->state = 200;
        echo json_encode([
            'status'  => $this->status,
            'reason'  => $this->reason,
            'state'   => $this->state,
            'results' => $results,
        ]);
    }

}
