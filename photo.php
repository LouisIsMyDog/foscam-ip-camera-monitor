<?php

// define root directory
if(!defined('__ROOT__')) {
	define('__ROOT__', $_SERVER['DOCUMENT_ROOT']);
}

require_once(__ROOT__ . '/CCTV/includes/initialize.php');

if(!$session->is_logged_in()) {	redirect_to("login.php"); }

if(empty($_GET['id'])) {

	$session->message("No photograph ID was provided.");
	redirect_to('index.php');
}

if(!isset($_GET['page'])) {
	$page = '';
} else {
	$page = $_GET['page'];
}

$photo = Snapshot::find_by_id($_GET['id']);

if(!$photo) {
	$session->message("The photo could not be located.");
	redirect_to('index.php');
}

if(!empty($session->date_filter)) {
	$where = $session->date_filter;
	
} else {$where = '';}

$sql  = "SELECT id FROM snapshots AS s, permissions AS p  WHERE p.user_id={$session->user_id} AND s.group_id=p.group_id {$where} ORDER BY time DESC";

$result_set = $database->query($sql);

$photo_id_array = mysqli_fetch_all($result_set);

include_layout_template("header.php");

$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;

$per_page = 1;

$total_count = Snapshot::count_all($where);

$pagination = new Pagination($page, $per_page, $total_count);



?>

		<div class="row buffer-both"  id="content">
			<div class="col-xs-12 col-md-4 col-md-offset-2">
				<div class="caption" style="overflow: auto;">
					<h5 style="text-align:center;"><strong>Path:</strong></h5>
					<span><?php echo trim($photo->path) ."/". trim($photo->filename); ?></span>
				</div>
			</div>
			
			<div class="col-xs-12 col-md-2">
				<div class="caption">
					<h5 style="text-align: center;"><strong>Camera:</strong></h5>
					<span><?php echo ($photo->camera == "NVR") ? $photo->camera." ".$photo->channel : $photo->camera; ?></span> 
				</div>
			</div>
		
			<div class="col-xs-12 col-md-2">
				<div class="caption">
					<h5 style="text-align: center;"><strong>Type:</strong></h5>
					<span><?php echo $photo->type; ?></span>
				</div>
			</div>
	
		</div>



	<div class="row">
		<div class="col-md-12">
			<a href="<?php echo "index.php?page=".$page ?>">
				<img class="img-responsive img-rounded" title="Click to go back" src="<?php echo $photo->path ."/". $photo->filename; ?>" style="border:2px solid black; "/>
			</a>
	</div>
</div>





<?php

$display->pagination_image($pagination, $photo_id_array, $page);


include_layout_template("footer.php");

?>