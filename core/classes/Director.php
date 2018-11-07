<?php
class Director {
 private $controller;
 private $action;

 public function __construct($http = '') {

  // This splits the GET request into / pieces
  $request = explode('/', $http['url']);
  array_pop($request);
  var_dump($request);

  // This ensures that if you are on the root directory the user
  // is routed to the index.
  if (empty($request)) {
   $this->controller = 'Home';
   $this->action     = 'index';
   return;
  }

  $this->controller = $request[0];
  $this->action     = 'index';

 }

 public function createController() {

  if (class_exists($this->controller)) {
   $parents = class_parents($this->controller);
   if (in_array("Controller", $parents)) {
    if (method_exists($this->controller, $this->action)) {
     return new $this->controller($this->action, $this->request);
    }
   } else {
    echo 'does not exist';
    return;
   }
  } else {
   echo 'not found';
   return;
  }
 }
}