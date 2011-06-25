<?php
include '../lib/lib.php';
opendb();
if(isset($_POST['t'])) {
  $time = $_POST['t'];
} else {
  $time = 0;
}
if(isset($_POST['onair'])) {
  $id = $_POST['onair']; 
  $query = "
     create temporary table `loeschi` 
     select `value`, `playtime` 
     from `status` 
     where `param` = 'onair'";
  mysql_query($query) or header('HTTP/1.0 500 Internal Server Error');
  $query = "
     replace into `status` 
     (`param`, `value`, `playtime`) 
     select 'played', `value`, `playtime` from `loeschi`
     ";
  mysql_query($query) or header('HTTP/1.0 500 Internal Server Error');
  $query = "UPDATE `status` SET `value` = $id, `playtime` = sec_to_time($time) WHERE `param` = 'onair'";
  mysql_query($query) or header('HTTP/1.0 500 Internal Server Error');
}
if(isset($_POST['next'])) {
  $id = $_POST['next']; 
  $query = "UPDATE `status` SET `value` = $id, `playtime` = sec_to_time($time) WHERE `param` = 'next'";
  mysql_query($query) or header('HTTP/1.0 500 Internal Server Error');
}
if(isset($_POST['online'])) {
  $id = $_POST['online']; 
  $query = "UPDATE `status` SET `value` = $id, `playtime` = sec_to_time($time) WHERE `param` = 'online'";
  mysql_query($query) or header('HTTP/1.0 500 Internal Server Error');
}
closedb();
?>
