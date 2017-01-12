<?php
// define root directory
if(!defined('__ROOT__')) {
	define('__ROOT__', $_SERVER['DOCUMENT_ROOT']);
}


require_once( __ROOT__ . '/CCTV/includes/initialize.php');


class Display {

	protected $menu_items = array("Snapshots" => "/CCTV/index.php", "Refresh" => "/CCTV/refresh.php", "Logout" => "/CCTV/login.php?logout=true");
	

	public function menu() {
		$menu_items = $this->menu_items;
		
		$menu  = '<nav class="navbar navbar-inverse">'."\n\t".'<div class="container">'."\n";
		$menu .= "\t\t".'<ul class="nav nav-pills">'."\n";

		foreach ($menu_items as $key => $value):
			$menu .= "\t\t\t".'<li role="presentation">'."\n";
		$menu .= "\t\t\t"."<a href=\"{$value}\">";
		$menu .=  $key;
		$menu .= '</a>';
		$menu .= '</li>'."\n";

		endforeach;
		
		$menu .= "\t\t\t".'<li class="pull-right"><a>'. formatBytes($this->total_size()) . '</a></li>'."\n";
		$menu .= "\t\t".'</u>'."\n";
		$menu .= "\t</div>\n</nav>"."\n\n";
		echo $menu;

	}

	public function pagination_images($pagination) {

//		$pag  = "\n\t".'<div class="row buffer-both" id="content" >'."\n";
//		$pag .= "\t\t".'<div class="col-md-12" >'."\n";
		$pag = "\t\t\t".'<div class="buffer-both">'."\n\n";
		$pag .= "\t\t".'<nav>'."\n";
		$pag .= "\t".'<ul class="pagination">'."\n";
		if($pagination->total_pages() > 1) {

			if($pagination->has_previous_page()) {
				$pag.= "\t"."<li><a href=\"index.php?page=";
				$pag.= $pagination->previous_page();
				$pag.= "\"> &laquo; Previous </a></li> "."\n";
			}

			$mod = ceil( $pagination->total_pages() / 8);

			for ($i = 1; $i <= $pagination->total_pages(); $i++) {
				// Don't print all pages if we have many ...
				if (fmod($i, $mod) == 0 || $i == 1 || $i == $pagination->total_pages() || ($i >= $pagination->current_page -2 && $i <= $pagination->current_page +2)) {

					if ($pagination->current_page == $i) {

						$pag .= "\t\t"."<li> <a href=\"index.php?page={$i}\" style=\"text-decoration : underline;\">{$i}</a> </li>"."\n";
					} else {
						$pag .=  "\t\t"."<li> <a href=\"index.php?page={$i}\">{$i}</a> </li>"."\n";
					}
				}
			}

			if($pagination->has_next_page()) {
				$pag.= "\t\t"."<li><a href=\"index.php?page=";
				$pag.= $pagination->next_page();
				$pag.= "\"> Next &raquo; </a></li>"."\n" ;
			}

		}
		$pag .= "\t"."</ul>";
		$pag .= "\n\t\t</nav>";
        $pag .= "\n\n\t\t</div> <!-- .buffer-both --> \n";
		echo $pag;
	}

	public function pagination_image($pagination, $photo_id_array, $page) {
		
		
		$id_array = $this->get_prev_next($photo_id_array);
		
		$pag  = '<div style="padding:0px;" class="row buffer-both" id="content" >';
		$pag .= '<div class="col-md-12">';
		$pag .= '<div class="buffer-both">';
		$pag .= '<nav>';
		$pag .= '<ul class="pagination">';


		if( isset($id_array[0])  && $id_array[0] != '') {
			$pag.= " <li><a href=\"photo.php?id=";
			$pag.= $id_array[0];
			$pag.= "\"> &laquo; Previous </a></li> ";
		} 

		if( isset($id_array[1]) && $id_array[1] != '' ) {
			$pag.= " <li><a href=\"photo.php?id=";
			$pag.= $id_array[1];
			$pag.= "\"> Next &raquo; </a></li>" ;
		}


		$pag .= '</ul></nav></div></div></div>';

		echo $pag;

	}

	public function get_prev_next( $photo_id_array) {

		foreach($photo_id_array as $key => $array) {

			if (in_array($_GET['id'], $array)) {
				$current_key = $key;
			}
		}

		$previous_key = $current_key - 1;
		$next_key = $current_key + 1;

		foreach($photo_id_array as $key => $array) {

			if ($previous_key == $key) {
				$previous_id = $array[0];
			} 

			if ($next_key == $key) {
				$next_id = $array[0];
			} 


		}
			if(!isset($previous_id)) {$previous_id = "";}
			if(!isset($next_id)) {$next_id = "";}
		
			return $results = array($previous_id,$next_id);
	}
	
	public function my_time($time) {
		$unix = strtotime($time);
		return $myFormatForView = '<strong>'.date("D, d M Y h:i:s a", $unix) . '</strong>';
	}

	
	public function total_size() {
		global $database;
		$sql = "SELECT SUM(size) FROM snapshots";
		$result_set = $database->query($sql);
		$sum = mysqli_fetch_row($result_set);
		$sum = $sum[0];
		return $sum;
	}
	
	public function admin_menu() {
		global $session;
		if($session->is_admin) { 		 
			
			$value = end($this->menu_items);
			$key = key($this->menu_items);
			reset($this->menu_items);
			array_pop($this->menu_items);
			$this->menu_items['Admin'] = '/CCTV/admin/admin.php';
			$this->menu_items[$key] = $value;
		}
		
	}
	
	public function date_picker() {
		global $session;
		$date_html  = '<form method="post" class="form-inline">';
		$date_html .= '<div class="form-group"> <!-- Date input -->';
		$date_html .= '<div class="input-group date" id="datetimepicker1">';
		$date_html .= '<input type="text" class="form-control" name="date" placeholder="MM/DD/YYY" />';
		$date_html .= '<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>';
		$date_html .= '</div> <!-- .input-group .date -->';
		$date_html .= '</div> <!-- .form-group -->';
		$date_html .= '<div class="form-group"> <!-- Submit button -->';
		if($session->date_filter == '') {
        $date_html .= '<button class="btn btn-primary " name="submit" type="submit">Filter</button>';
        } else {
	    $date_html .= '<button class="btn btn-primary " name="submit" type="submit">Filter</button>';  
	    $date_html .= '</div> <!-- .form-group -->';  
	    $date_html .= '<button class="btn btn-default " formmethod="post" name="clear" value="clear" type="submit">Clear</button>'; 
	    }   
        $date_html .= '</div>';
		$date_html .= '</form>';
		echo $date_html;	
	}

}

$display = new Display();

?>
