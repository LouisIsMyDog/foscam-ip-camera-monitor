<?php

require_once "../index.php";

if (isset($_GET["page"])) {

    // $pagination = new Pagination($session->settings["page"], $per_page, $total_count);
    $settings = $session->getItem('settings');
    // logAction('API', implode(",",$settings), 'api.log');

    $settings['page']   = $_GET["page"];
    $settings['offset'] = ($settings['page'] - 1) * $per_page;

    $photos = Snapshot::ajaxRetrievePhotos($settings);
    $array  = array();
    $x      = 1;
    if (is_array($photos)):
        foreach ($photos as $photo) {

            $modalFooter = array(
                'model'    => $cameras[$photo->camera]['model'],
                'size'     => formatBytes($photo->size),
                'filename' => $photo->filename,
                'ID'       => $photo->id,
                'page'     => $session->settings['page'],
                'img'      => $x . '/' . $per_page,
            );

            $time    = $display->myTime($photo->time);
            $footer  = $display->modalFooter($modalFooter);
            $name    = $photo->path . "/" . $photo->filename;
            $html    = "<div data-remote=\"{$name}\" data-toggle=\"lightbox\" data-gallery=\"snapshots\" data-type=\"image\" data-footer=\"{$footer}\" data-title=\"<strong>{$time}</strong>\"></div>";
            $array[] = $html;
            $x++;
        }
    endif;

    // logAction('API', implode(",",$array), 'api.log');

    $array = json_encode($array);

    echo $array;
}
