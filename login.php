<?php

require_once 'index.php';

if (!$result = $db->query("SELECT * FROM users WHERE username = '$defaultUser[user]';", false)) {
    $creat_DB->start();
    redirectTo("login.php");
} else {

    if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
        $session->logout();
    }

    if ($session->isLoggedIn()) {
        redirectTo("home.php");
    }

    if (isset($_POST['submit'])) {

        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        $found_user = User::authenticate($username, $password);

        if ($found_user && $found_user->username == $username && $found_user->password == $password) {
            $session->login($found_user);
            logAction('Login', "{$found_user->username} logged in.", "login.log");
            if (!empty($_POST['remember-me'])) {
                setcookie("username", $_POST['username'], time() + (7 * 24 * 60 * 60));
            } else {
                if (isset($_COOKIE['username'])) {
                    setcookie("username", "");
                }
                redirectTo("login.php");
            }
            redirectTo("home.php");
        } else {
            $message = "Failed.";
        }

// if submit is not set then do this
    } else {
        $message  = "Please log in.";
        $username = "";
        $password = "";
    }

    ?>
<?php includeLayoutTemplate("header.php");?>
<h1 align="center" class="h1" style="color:#286090;">
    <?php echo $title; ?>
</h1>
<form class="form-signin" action="/CCTV/login.php" method="post">
    <?php if ($message == "Failed.") {?>
    <div class="alert alert-danger" role="alert">
        <?php }?>
        <h2 class="form-signin-heading">
            <?php echo $message; ?>
        </h2>
        <div class="form-group">
            <label class="sr-only" for="username">Username:</label>
            <input type="text" class="form-control" placeholder="Username" name="username" id="username" value="<?php echo isset($_COOKIE['username']) ? htmlspecialchars($_COOKIE['username']) : htmlspecialchars($username); ?>" />
        </div>
        <div class="form-group">
            <label class="sr-only" for="password">Password:</label>
            <input type="password" class="form-control" placeholder="Password" name="password" id="password" value="" />
        </div>
        <div class="checkbox">
            <label><input type="checkbox" name="remember-me" value="remember-me" />Remember me</label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit" value="Submit">Sign in</button>
        <?php if ($message == "Failed.") {?>
    </div>
    <?php }?>
</form>
<?php
includeLayoutTemplate("footer.php");

}
?>