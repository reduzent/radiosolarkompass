<?php

include '../lib/lib.php';
opendb();

if(isset($_POST['try'])) {
  $try_id = $_POST['try']; 
  $query = "SELECT `trycnt` FROM `radios` WHERE `id` = $try_id limit 1";
  $result = mysql_query($query) or header('HTTP/1.0 500 Internal Server Error');
  while(list($trycnt) = mysql_fetch_array($result)) {
    $trycnt += 1;
    /* echo $ort, $num_of_trial, "trial" ;*/
    $update_query = "UPDATE `radios` SET `trycnt` = $trycnt WHERE `id` = $try_id LIMIT 1 ";
    mysql_query($update_query) or header('HTTP/1.0 500 Internal Server Error');
  }
}

if(isset($_POST['play'])) {
  $play_id = $_POST['play'] ; 
  $query = "SELECT `playcnt` FROM `radios` WHERE `id` = $play_id limit 1";
  $result = mysql_query($query) or header('HTTP/1.0 500 Internal Server Error');
  while(list($playcnt) = mysql_fetch_array($result)) {
    $playcnt += 1;
    /* echo $ort, $num_of_trial, "trial" ;*/
    $update_query = "UPDATE `radios` SET `playcnt` = $playcnt WHERE `id` = $play_id LIMIT 1 ";
    mysql_query($update_query) or header('HTTP/1.0 500 Internal Server Error');
  }
}

closedb();
?>
