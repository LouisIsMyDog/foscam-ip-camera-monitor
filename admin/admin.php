<?php
// define root directory
if(!defined('__ROOT__')) {
	define('__ROOT__', $_SERVER['DOCUMENT_ROOT']);
}


require_once( __ROOT__ . '/CCTV/includes/initialize.php');
if( !$session->is_admin ) {	redirect_to("/CCTV/login.php"); }

$users = User::find_all();
	
?>
<?php include_layout_template("header.php"); ?>
<?php if(isset($session->message) && $session->message != "") {
	$alert  = "<div class=\"alert ";
	$alert .= strpos($session->message, 'not') !== false ? " alert-danger" : " alert-success"; 
	$alert .= "\" role=\"alert\">";
	$alert .= $session->message;
	$alert .= "</div>";
	 echo $alert;
}
?>
<div class="row">
	<div class="col-md-6 col-md-offset-3">

<h2>Registered Users</h2>
	
  <div class="table-responsive-vertical shadow-z-1">
	
	<table id="table" class="table table-hover table-mc-light-blue">
	  
	  <thead>
		  <tr>
			   <th>ID</th>
		       <th>Username</th>
		   	   <th>Password</th>
			   <th>First Name</th>
			   <th>Last Name</th>
			   <th>Delete</th>
		  </tr>		
	  </thead>
	
	  <tbody>
		<?php $x=1;?>
		<?php foreach ($users as $user):?>
					<tr>
						<td data-title="ID"><?php echo $user->id; ?></td>
						<td data-title="Username"><?php echo $user->username; ?></td>
						<td data-title="Password"><?php echo $user->password; ?></td>
						<td data-title="First Name"><?php echo $user->first_name; ?></td>
						<td data-title="Last Name"><?php echo $user->last_name; ?></td>
						<td data-title="Delete"><?php echo ($user->id!=1) ? '<a href="form_processing.php?id=' . $user->id . '" style="color:red;">Delete</a></td>' : '<span class="text-primary">Admin</span></td>';
						?>
					</tr>	
			<?php $x++ ?>
			<?php endforeach; ?>
	  </tbody>
		
	</table>

		</div> <!-- .table-responsive-vertical .shadow-z-1 -->
	</div>  <!-- .col-md-6 .col-md-offset-3 -->
</div> <!-- .row -->

<div class="row">
	<div class="col-md-6 col-md-offset-3">
		<h2>Add New User</h2>
	  <div class="admin_form shadow-z-1">
		<form action="/CCTV/admin/form_processing.php" method="post">
			<div class="col-sm-6 col-xs-12">
				<div class="form-group clearfix">
					<input type="text" class="form-control" id="username" placeholder="Username" name="username" value="">
				</div>
			</div>
			<div class="col-sm-6 col-xs-12">
				<div class="form-group clearfix">
					<input type="text" class="form-control" id="password" placeholder="Password" name="password" value="">
				</div>
			</div>
			<div class="col-sm-6 col-xs-12">
				<div class="form-group clearfix">
					<input type="text" class="form-control" id="first_name" placeholder="First Name" name="first_name" value="">
				</div>
			</div>
			<div class="col-sm-6 col-xs-12">
				<div class="form-group clearfix">
					<input type="text" class="form-control" id="last_name" placeholder="Last Name" name="last_name" value="">
				</div>
			</div>
			<div class="col-sm-12 col-xs-12">
				<h4>Select Cameras:</h4>
			</div>
			<div class="form-group clearfix">
			<?php $x=0; foreach ($group_ids as $key => $value): ?>
				<div class="col-sm-6 col-xs-12">
			   
				  <div class="checkbox">
					<label><input type="checkbox" name="camera[]" id="inlineCheckbox<?php echo $x; ?>" value="<?php echo $key; $x++;?>"><?php echo trim($value);?></label>
				  </div>
				
				</div>	
			<?php endforeach;?>
			</div>
			<div class="col-sm-12">
				<button class="btn btn-default" type="submit" name="post" value="submit" id="admin_submit" >Submit</button>
			</div>	
			
		</form>
		
	  </div>	
	</div>  <!-- .col-md-6 .col-md-offset-3 -->
</div> <!-- .row -->

<div class="row">
	<div class="col-md-6 col-md-offset-3">

		
	</div>  <!-- .col-md-6 .col-md-offset-3 -->
</div> <!-- .row -->

<?php include_layout_template("footer.php"); ?>