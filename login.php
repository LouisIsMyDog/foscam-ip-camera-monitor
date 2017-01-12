<?php

// define root directory
if(!defined('__ROOT__')) {
	define('__ROOT__', $_SERVER['DOCUMENT_ROOT']);
}

require_once( __ROOT__ . '/CCTV/includes/initialize.php');


if(!$result = $db->query("SELECT * FROM users WHERE username = '$admin_user';", false) ) {
	$creat_DB->start();
	redirect_to("login.php");
} else {

if ( isset($_GET['logout']) && $_GET['logout'] == 'true' ){
	$session->logout();
}


if( $session->is_logged_in() ) {
	redirect_to("/CCTV/index.php");
}



if (isset($_POST['submit'])) {

	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	$found_user = User::authenticate($username,$password);
	
	if ($found_user && $found_user->username == $username && $found_user->password == $password) {
		$session->login($found_user);
		log_action('Login', "{$found_user->username} logged in.", "login.log");
			if(!empty($_POST['remember-me'])) {
				setcookie("username", $_POST['username'], time()+ ( 7 * 24 * 60 * 60 ) );
			} else {
				if(isset($_COOKIE['username'])) {
					setcookie("username","");
				}
				redirect_to("/CCTV/login.php");
			}
		redirect_to("/CCTV/index.php");
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

<?php include_layout_template("header.php"); ?>

					<h1 align="center" class="h1" style="color:#286090;"><?php echo $title; ?></h1>

					<form class="form-signin" action="/CCTV/login.php" method="post">

						<?php if($message == "Failed.") { ?>
								<div class="alert alert-danger" role="alert">
						<?php } ?>

						<h2 class="form-signin-heading"><?php echo $message; ?></h2>
						<div class="form-group">
							<label class="sr-only" for="username">Username:</label>
							<input type="text" class="form-control" placeholder="Username" name="username" id="username" value="<?php echo isset($_COOKIE['username']) ? htmlspecialchars($_COOKIE['username']) : htmlspecialchars($username); ?>" />
						</div>
						<div class="form-group">
							<label class="sr-only" for="password">Password:</label>
							<input type="password" class="form-control" placeholder="Password" name="password" id="password" value="" />
						</div>
						
						<div class="checkbox">
						 <label><input type="checkbox" name="remember-me" value="remember-me"/>Remember me</label>
						</div>
						
					<button class="btn btn-lg btn-primary btn-block" type="submit" name="submit" value="Submit" >Sign in</button>
						<?php if($message == "Failed.") { ?>
	 							</div>
	 				    <?php } ?>

 					</form>
				
<?php 
	include_layout_template("footer.php"); 
	
	}
?>