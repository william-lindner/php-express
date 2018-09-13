<?php
class Director {
 private $request;
 private $controller;
 private $action;

 public function __construct($http = '') {

  var_dump($http);

  // This splits the GET request into / pieces
  $request = explode('/', $http['url']);
  var_dump($request);
  array_pop($request);

  var_dump($request);

  $this->request = $http;

  // This ensures that if you are on the root directory the user
  // is routed to the index
  if (empty($request)) {
   $this->controller = 'Home';
   $this->action     = 'index';
   return;
  }

  if ($this->request['controller'] === '') {
   $this->controller = 'home';
  } else {
   $this->controller = $this->request['controller'];
  }

  if ($this->request['action'] === '') {
   $this->action = 'index';
  } else {
   $this->action = $this->request['action'];
  }

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