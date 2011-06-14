<?php
include '../lib/lib.php';
opendb();

$city_id = 1;
if (isset($_GET['id'])) {
  $city_id = $_GET['id'];
}
pdGetStreamsOfLocation($city_id);
closedb();
?>

