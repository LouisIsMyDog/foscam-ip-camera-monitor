<?php

// define root directory
if(!defined('__ROOT__')) {
	define('__ROOT__', $_SERVER['DOCUMENT_ROOT']);
}

require_once(__ROOT__ . '/CCTV/includes/initialize.php');


if(!$session->is_logged_in()) {	redirect_to("/CCTV/login.php"); }

$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;

$per_page = 32;

// Below is used for the filter option

if(isset($_POST['submit'])) {
		
	$date = $_POST['date'];
	$_GET['page'] = 1;
	$page = $_GET['page'];
	
	  if($date != '') {	  
		$where = "AND date(time) = '" . date("Y-m-d",strtotime($date)) ."'";
		$session->date_filter($where);
		$session->date_filter = $where;	
		} else { 
			unset($where);
			unset($_SESSION['date_filter']); 
			$where = ''; 
			$session->date_filter = $where;
	    }
}

if(!isset($_POST['submit'])) {
 
	$where = $session->date_filter;
	
}

if(isset($_POST['clear']) && ($_POST['clear']== 'clear')) {
		unset($where);
		unset($_SESSION['date_filter']); 
		$where = ''; 
		$session->date_filter = $where;
}
/**-Filter ends here-**/

$total_count = Snapshot::count_all($where);

$pagination = new Pagination($page, $per_page, $total_count);

$sql = "SELECT * FROM snapshots AS s, permissions AS p  WHERE p.user_id={$session->user_id} AND s.group_id=p.group_id {$where} ORDER BY time DESC LIMIT {$per_page} OFFSET {$pagination->offset()}";

/*
$sql  = "SELECT * FROM snapshots ";
$sql .= "ORDER BY time DESC ";
$sql .= "LIMIT {$per_page} ";
$sql .= "OFFSET {$pagination->offset()}";
*/
$photos = Snapshot::find_by_sql($sql);

include_layout_template("header.php");

$user = new user();
$user->populate_values($session->user_id);

if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'login')) {
?>
<div class="alert alert-success alert-dismissible fade in" role="alert">
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
  <span aria-hidden="true">&times;</span>
</button>
<p>Welcome <strong><?php echo $user->full_name(); ?></strong> to my snapshot gallery.</p>
</div>
<?php } ?>

<div class="buffer-both" id="content" >
   <div class="row">	
	<div class="col-md-12" >
		
<?php echo !empty($photos) ? $display->pagination_images($pagination) : "<h4><strong>No Results.</strong></h4>"; ?>
	</div> <!-- .col-md-12 -->
   </div> <!-- .row -->
   <div class="row">	
	<div style="margin-bottom: 1em;" class="col-sm-8 col-md-offset-2">
		<?php $display->date_picker(); ?>
	</div>
   </div> <!-- .row -->	
</div>	<!-- .row buffer-both -->

<?php
$x=1;	
foreach($photos as $photo) :

echo ($x == 1 )  ? '<div class="row">' : ''; ?>

	<div class="col-xs-12 col-md-3">
		<div class="thumbnail">
			<a href="photo.php?id=<?php echo $photo->id."&page=".$page; ?>">
				<img class="img-responsive img-rounded" title="Click to enlarge" src="<?php echo $photo->path ."/". $photo->filename; ?>" />
			</a>
			<div class="caption">  
				<span><?php echo $display->my_time($photo->time); ?></span>
			</div> <!-- .caption -->
		</div> <!-- .thumbnail -->
	</div> <!-- .col-xs-12 .col-md-3 -->
	<?php
	echo ($x%4 == 0) && ($x != 1) && ($x != 32 )  ? '</div> <!-- .row -->' . "\n\n" . '<div class="row">' : '';
	
	$x++;
		
endforeach;
?>

</div> <!-- .row -->

<div class="row buffer-both" id="content" >
	<div class="col-md-12" >
<?php $display->pagination_images($pagination); ?>
   </div> <!-- .col-md-12 -->
</div>  <!-- .row buffer-both -->
	

	
<?php

include_layout_template("footer.php");

?>
