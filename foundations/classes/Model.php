<?php
/*

@Name: MTS Model Class
@Author: William Lindner
@Version: 1.5
@License: MIT

Copyright (c) 2018 william-lindner

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

-----------------------------------------------------

 */

abstract class Model {
 protected $db;
 protected $request;

 protected $status = 'unknown';
 protected $reason = 'unknown';
 protected $state  = 501; # https://www.w3schools.com/tags/ref_httpmessages.asp

 private $dataType = 'json';
 private $data;

 public function __construct($dataType = 'json') {
  // setup the database connection
  $this->db = new db_connect();
 }
 public function request($request = null, $data = []) {

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

 protected function returnError($reason, $state = 501) {
  // set the status to an error state
  $this->status = 'error';
  $this->reason = $reason;
  $this->state  = $state;
  $this->echoResults();
 }

 protected function echoResults($results = [], $dataType = 'json') {
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
