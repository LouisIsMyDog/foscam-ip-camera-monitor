<?php

require_once '../index.php';

if (!$session->isLoggedIn()) {redirectTo("login.php");}

if (isset($_POST['post'])) {
    $user = new User();
    $user->username = $_POST['username'];
    $user->password = $_POST['password'];
    $user->first_name = $_POST['first_name'];
    $user->last_name = $_POST['last_name'];

    if ($user->save()) {
        if (isset($permissions)) {
            //$p = $permissions->set_permissions($user->id, $_POST['camera']) ? " Permissions were created" : " Permissions were not created.";
            $permissions->setPermissions($user->id, $_POST['camera']);
            $p = "";
            $session->message("User {$user->username} added successfully. {$p}");
            redirectTo('admin.php');
        } else {
            $message = "Failed to add user {$user->username}.";
        }
    }
}

if (isset($_GET['id'])) {
    $user_d = User::findById($_GET['id']);

    if ($permissions->deletePermissions($_GET['id'])) {
        if ($user_d && $user_d->delete()) {
            $session->message("The user {$user_d->username} was deleted.");
            redirectTo('admin.php');
        } else {
            $session->message("The user could not be deleted.");
            redirectTo('admin.php');
        }
    } else {
        $session->message("The user permissions could not be deleted.");
        redirectTo('admin.php');
    }

}

if (isset($_POST['data'])) {
    $data = $_POST['data'];
    $session->message("All data was removed!");
    $readFiles->deleteAllFiles();
    $snapshot = new Snapshot;
    $snapshot->eraseSnapshots();
    redirectTo('admin.php');
    unlink(__APP__ . "/logs/info.log");

}

if (isset($database)) {$database->closeConnection();}
