<?php


require_once 'index.php';

if (!$session->isLoggedIn()) {redirectTo("login.php");}

$readFiles->cameraIndex(Snapshot::$cameras);
redirectTo('home.php');
