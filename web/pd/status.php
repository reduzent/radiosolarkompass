<?php

include '../lib/lib.php';
opendb();

if(isset($_POST['onair'])) {
  $id = $_POST['onair']; 
  $query = "UPDATE `status` SET `value` = $id WHERE `param` = 'onair'";
  mysql_query($query) or header('HTTP/1.0 500 Internal Server Error');
}

if(isset($_POST['next'])) {
  $id = $_POST['next']; 
  $query = "UPDATE `status` SET `value` = $id WHERE `param` = 'next'";
  mysql_query($query) or header('HTTP/1.0 500 Internal Server Error');
}

closedb();
?>
