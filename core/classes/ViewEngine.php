<?php
/*

@Name: MTS ViewEngine Class
@Author: William Lindner
@Version: 1.0
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

abstract class ViewEngine {
 protected $viewPath;
 protected $widgetPath;

 private $viewContents;
 private $view;

 public function __construct($viewPath, $relative = true) {
  // setup the location for the views / templates
  if ($relative) {
   $this->viewPath = $_SERVER['DOCUMENT_ROOT'] . $viewPath;
  } else {
   $this->viewPath = $viewPath;
  }
  $this->widgetPath = $_SERVER['DOCUMENT_ROOT'] . '/widgets/';
 }

 public function loadView($view, $data = []) {
  $fullViewPath = $this->viewPath . $view;

  // check to see if the file is actually there
  if (!file_exists($fullViewPath)) {
   throw new \Exception('Unable to locate view file.', 404); # https://www.w3schools.com/tags/ref_httpmessages.asp
  }

  $this->viewContents = @file_get_contents($fullViewPath);
  $this->view         = $this->replaceTokens($data);
  echo $this->view;
 }

 final private function replaceTokens($data = []) {
  // check if tokens exist and that data has been passed
  $tokensExist = preg_match('/({{[^}]*}})/', $this->viewContents);
  if (!$data || !$tokensExist) {
   return $this->viewContents;
  }
  $viewMinusTokens = preg_split('/({{[^}]*}})/', $this->viewContents);
  preg_match_all('/({{[^}]*}})/', $this->viewContents, $viewTokens);
  $viewTokens = $viewTokens[0];
  // loop through tokens for things to replace
  for ($i = 0; $i < count($viewTokens); $i++) {
   $viewToken = &$viewTokens[$i];
   if (strpos($viewToken, '::')) {
    // When function :: dataset syntax is used
    // find the function in the class.
    $functionArray = explode('::', $viewToken);
    $function      = substr($functionArray[0], 2);
    $dataKey       = substr($functionArray[1], 0, -2);
    if (!method_exists($this, $function)) {
     // needs to throw exception
     echo 'No method to handle data reference<br>';
    }
    if (!isset($data[$dataKey])) {
     // needs exception
     echo 'Data point does not exist';
    }
    // load the method - passing the data
    $viewToken = $this->{$function}($data[$dataKey]);
   } elseif (strpos($viewToken, '>')) {
    // widget loader
   } else {
    $token = substr($viewToken, 2, -2);
    if (!isset($data[$token])) {
     echo 'Woops...<br>';
     //throw new \Exception("Not all tokens replaced.", 206); # https://www.w3schools.com/tags/ref_httpmessages.asp
    }
    $viewToken = $data[$token];
   }
  }

  $compiledView = '';
  // reassemble the view for export
  for ($i = 0; $i < count($viewMinusTokens); $i++) {
   $compiledView .= $viewMinusTokens[$i] . $viewTokens[$i];
  }
  return $compiledView;
 }

 public function createWidget($object) {

 }
}

/*

{{standard_token}}

{{>widget_token}}

{{method::key}}
 */
