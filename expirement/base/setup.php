<?php

use App\Middleware\Authenticate;
use App\Middleware\Authorize;
use App\Middleware\HubDirect;
use App\Middleware\RegionIdentification;
use Teapot\Request;
use Teapot\Teapot;
use Teapot\Visitor;

define('__BASEDIR__', __DIR__ . '/..');
define('__VIEWDIR__', __DIR__ . '/../resources/views');

require __BASEDIR__ . '/vendor/autoload.php';
require __DIR__ . '/helpers/debuggers.php';
require __DIR__ . '/helpers/inquirers.php';
require __DIR__ . '/helpers/loaders.php';
require __DIR__ . '/helpers/filters.php';

$request = new Request($_SERVER);
$teapot  = new Teapot($request, new Visitor());

$teapot->beforeSteeping(Authenticate::class);
$teapot->beforeSteeping(RegionIdentification::class);
$teapot->beforeSteeping(HubDirect::class);
$teapot->beforeSteeping(Authorize::class);

$teapot->afterParty(function ($request, $visitor) {
    Authenticate::destroy();
});

//! This is not how this will be finalized - thrown in to meet prior dev build
if (isset($_GET['logout'])) {
    $teapot->endParty();
    die;
}

$teapot->steep()->pour();

set_include_path($_SERVER['DOCUMENT_ROOT']);

// ---
//! All code below should be removed ASAP - with view system
define("ASSET_PATH", $_SERVER['DOCUMENT_ROOT'] . "/../assets");

$ajax = $ajax ?? false;

if ($_SERVER['REQUEST_METHOD'] != 'POST' && !$ajax) {
    view('partials/head');
}
