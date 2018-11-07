<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/../assets/setup.php';

$router     = new Director($_GET);
$controller = $router->createController();
if ($controller) {
 $controller->executeAction();
 $controller->returnView();
}