<?php
include '../lib/lib.php';
opendb();

$location_id = 1;
if (isset($_GET['id'])) {
  $location_id = $_GET['id'];
}
pdGetStreamsOfLocation($location_id);
closedb();
?>

