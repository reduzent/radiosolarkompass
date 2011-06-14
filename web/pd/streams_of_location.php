<?php
include '../lib/lib.php';
opendb();

$city_id = '_1';
if (isset($_GET['id'])) {
  $city_id = $_GET['id'];
}
$city_id = str_replace('_', '', $city_id);
pdGetStreamsOfLocation($city_id);
closedb();
?>

