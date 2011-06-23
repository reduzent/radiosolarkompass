<?php
include '../lib/lib.php';
opendb();

$radio = '0';
if (isset($_GET['radio'])) {
  $radio = $_GET['radio'];
}
pdGetAnnounceData($radio);
closedb();
?>

