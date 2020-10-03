<?php

class Display
{

    protected $menu_items = array("Snapshots" => "/CCTV/home.php", "Refresh" => "/CCTV/refresh.php", "Logout" => "/CCTV/login.php?logout=true");

    public function menu()
    {
        $menu_items = $this->menu_items;

        $menu = '<nav class="navbar navbar-inverse">' . "\n\t" . '<div class="container">' . "\n";
        $menu .= "\t\t" . '<ul class="nav nav-pills">' . "\n";

        foreach ($menu_items as $key => $value):
            $menu .= "\t\t\t" . '<li role="presentation">' . "\n";
            $menu .= "\t\t\t" . "<a href=\"{$value}\">";
            $menu .= $key;
            $menu .= '</a>';
            $menu .= '</li>' . "\n";

        endforeach;

        $menu .= "\t\t\t" . '<li class="pull-right"><a>' . formatBytes($this->totalSize()) . '</a></li>' . "\n";
        $menu .= "\t\t" . '</u>' . "\n";
        $menu .= "\t</div>\n</nav>" . "\n\n";
        echo $menu;

    }

    public function paginationImages($pagination)
    {

        $pag = "\t\t\t" . '<div class="buffer-both">' . "\n\n";
        $pag .= "\t\t" . '<nav>' . "\n";
        $pag .= "\t" . '<ul class="pagination">' . "\n";
        if ($pagination->totalPages() > 1) {

            if ($pagination->hasPreviousPage()) {
                $pag .= "\t" . "<li><a href=\"home.php?page=";
                $pag .= $pagination->previousPage();
                $pag .= "\"> &laquo; Previous </a></li> " . "\n";
            }

            $mod = ceil($pagination->totalPages() / 8);

            for ($i = 1; $i <= $pagination->totalPages(); $i++) {
                // Don't print all pages if we have many ...
                if (fmod($i, $mod) == 0 || $i == 1 || $i == $pagination->totalPages() || ($i >= $pagination->current_page - 2 && $i <= $pagination->current_page + 2)) {

                    if ($pagination->current_page == $i) {

                        $pag .= "\t\t" . "<li> <a href=\"home.php?page={$i}\" style=\"text-decoration : underline;\">{$i}</a> </li>" . "\n";
                    } else {
                        $pag .= "\t\t" . "<li> <a href=\"home.php?page={$i}\">{$i}</a> </li>" . "\n";
                    }
                }
            }

            if ($pagination->hasNextPage()) {
                $pag .= "\t\t" . "<li><a href=\"home.php?page=";
                $pag .= $pagination->nextPage();
                $pag .= "\"> Next &raquo; </a></li>" . "\n";
            }

        }
        $pag .= "\t" . "</ul>";
        $pag .= "\n\t\t</nav>";
        $pag .= "\n\n\t\t</div> <!-- .buffer-both --> \n";
        echo $pag;
    }

    public function paginationImage($pagination, $photo_id_array, $page)
    {

        $id_array = $this->getPrevNext($photo_id_array);

        $pag = '<div style="padding:0px;" class="row buffer-both" id="content" >';
        $pag .= '<div class="col-md-12">';
        $pag .= '<div class="buffer-both">';
        $pag .= '<nav>';
        $pag .= '<ul class="pagination">';

        if (isset($id_array[0]) && $id_array[0] != '') {
            $pag .= " <li><a href=\"photo.php?id=";
            $pag .= $id_array[0];
            $pag .= "\"> &laquo; Previous </a></li> ";
        }

        if (isset($id_array[1]) && $id_array[1] != '') {
            $pag .= " <li><a href=\"photo.php?id=";
            $pag .= $id_array[1];
            $pag .= "\"> Next &raquo; </a></li>";
        }

        $pag .= '</ul></nav></div></div></div>';

        echo $pag;

    }

    public function getPrevNext($photo_id_array)
    {
        $current_key = array_search(array(0 => $_GET['id']), $photo_id_array);

        $previous_key = ($current_key > 0) ? $current_key - 1 : null;
        $next_key     = $current_key + 1;

        $previous_id = (isset($previous_key)) ? $photo_id_array[$previous_key][0] : '';
        $next_id     = (!empty($photo_id_array[$next_key][0])) ? $photo_id_array[$next_key][0] : '';

        return $results = array($previous_id, $next_id);
    }

    public function myTime($time)
    {
        $unix                   = strtotime($time);
        return $myFormatForView = '<strong>' . date("D, M d Y h:i:s a", $unix) . '</strong>';
    }

    public function totalSize()
    {
        global $database;
        $sql        = "SELECT SUM(size) FROM snapshots";
        $result_set = $database->query($sql);
        $sum        = mysqli_fetch_row($result_set);
        $sum        = $sum[0];
        return $sum;
    }

    public function adminMenu()
    {
        global $session;
        if ($session->is_admin) {

            $value = end($this->menu_items);
            $key   = key($this->menu_items);
            reset($this->menu_items);
            array_pop($this->menu_items);
            $this->menu_items['Admin'] = '/CCTV/admin/admin.php';
            $this->menu_items[$key]    = $value;
        }

    }

    public function datePicker()
    {
        global $session;
        $date_html = '<form method="post" class="form-inline">';
        $date_html .= '<div class="input-group date" id="datetimepicker">';
        $date_html .= '<input type="text" class="form-control" name="date" placeholder="MM/DD/YYYY" data-date-start-date="' . $this->getListOfImageDates()["startDate"] . '" >';
        $date_html .= '<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>';
        $date_html .= '</div> <!-- .input-group .date -->';
        $date_html .= '<div class="form-group"> <!-- Submit button -->';
        $date_html .= '<button class="btn btn-primary " name="submit" type="submit">Filter</button>';
        if ($session->settings['dateFilter'] == true) {
            // $date_html .= '</div> <!-- .form-group -->';
            $date_html .= '<button class="btn btn-default" formmethod="post" name="clear" value="clear" type="submit">Clear</button>';
        }
        $date_html .= '</div>';
        $date_html .= '</form>';
        echo $date_html;
    }

    public function modalFooter($array)
    {
        $output = "";
        foreach ($array as $key => $value) {
            $output .= "<span class='modalItemName item-{$key}-name'>" . ucwords($key) . ":</span>\t";
            $output .= "<span class='modalItemValue item-{$key}-value'>" . $value . "</span>\t";
        }
        return $output;
    }

    private function getListOfImageDates()
    {
        $dates = Snapshot::getDates();
        $x     = 0;
        $count = count($dates);
        foreach ($dates as $key => $value) {
            $dates[$x] = $value[0];
            $x++;
        }

        $endDate   = date("m/d/Y", strtotime($dates[($count - 1)]));
        $startDate = date("m/d/Y", strtotime($dates[0]));
        $startDate = strtotime(date("m/d/Y")) - strtotime($startDate);
        $startDate = number_format(date("d", $startDate)) * (-1) . "d";
        return array('endDate' => $endDate, 'startDate' => $startDate);
        // print_r($dates);
    }

}

$display = new Display();
