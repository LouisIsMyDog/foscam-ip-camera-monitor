<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

<head>
    <title>Foscam Security Camera</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/CCTV/includes/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/CCTV/includes/assets/css/style.css?ver=<?php echo time(); ?>" rel="stylesheet">
    <!-- <link href="/CCTV/node_modules/lightbox2/dist/css/lightbox.min.css" rel="stylesheet"> -->
    <link href="/CCTV/node_modules/bower/bin/bower_components/ekko-lightbox/dist/ekko-lightbox.css" rel="stylesheet">
    <link href="/CCTV/includes/assets/bootstrap-datepicker-1.6.1-dist/css/bootstrap-datepicker3.css" rel="stylesheet">
    <?php
if (basename($_SERVER['PHP_SELF']) == 'login.php') {
    echo '<link href="/CCTV/includes/assets/css/login.css" rel="stylesheet">' . "\n";
}
?>
</head>

<body>
    <?php
if (basename($_SERVER['PHP_SELF']) != 'login.php') {
    global $display;
    $display->adminMenu();
    $display->menu();
}
?>
    <div class="container-fluid">
        <!-- header template -->