<?php

namespace Teapot;

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

    // NOTE: Request should only be used in the context of AJAX with the current builds.
    // NOTE: You can always call the methods directly if you want an array of results.
    public function request($request = null, $data = [])
    {
        $this->state = 501;
        // There are occasions when a request is passed as part of the array.
        // This mostly occurs when AJAX is involved. The following code handles this use case.
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
        exit;
    }

}
