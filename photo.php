<?php

require_once 'index.php';

if (!$session->isLoggedIn()) {redirectTo("login.php");}

if (empty($_GET['id'])) {

    $session->message("No photograph ID was provided.");
    redirectTo('index.php');
}

if (!isset($_GET['page'])) {
    $page = '';
} else {
    $page = $_GET['page'];
}

$photo = Snapshot::findById($_GET['id']);

if (!$photo) {
    $session->message("The photo could not be located.");
    redirectTo('index.php');
}

if (!empty($session->dateFilter)) {
    $where = $session->dateFilter;
    $order = 'ASC';

} else { $where = ''; $order = 'DESC';}

$settings = array(
            'where' => $where,
            'order' => $order,
            'user_id' => $session->user_id,
          );

$photo_ids = Snapshot::retrievePhotoIDS($settings);

includeLayoutTemplate("header.php");

$page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;

$per_page = 1;

$total_count = Snapshot::countAll($where);

$pagination = new Pagination($page, $per_page, $total_count);

?>
<div class="row">
    <div class="col-md-6">
        <div class="table-responsive-vertical stop-overflow">
            <table id="table" class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Path:</th>
                        <th scope="col">Model:</th>
                        <th scope="col">Location:</th>
                        <th scope="col">Type:</th>
                        <th scope="col">Size:</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-title="Path:">
                            <?php echo trim($photo->path) . "/" . trim($photo->filename); ?>
                        </td>
                           </td>
                            <td data-title="Model:">
                            <?php echo $cameras[$photo->camera]['model']; ?>
                        </td>
                        <td data-title="Location:">
                            <?php echo ($photo->camera == "NVR") ? $photo->camera . " " . $photo->channel : $photo->camera; ?>
                        </td>
                        <td data-title="Type:">
                            <?php echo $photo->type; ?>
                        </td>
                        <td data-title="Size:">
                            <?php echo formatBytes($photo->size); ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <a href="<?php echo $photo->path . "/" . $photo->filename; ?>" data-lightbox="snapshot" data-title="Foscam IP Camera Snapshot Image">
            <!-- <a href="<?php //echo " index.php?page=" . $page; ;;;;;?>"> -->
            <img class="img-responsive img-rounded" title="Click to go back" src="<?php echo $photo->path . "/" . $photo->filename; ?>" style="border:2px solid black; "/>
        </a>
    </div>
</div>
<?php

$display->paginationImage($pagination, $photo_ids, $page);

includeLayoutTemplate("footer.php");

?>