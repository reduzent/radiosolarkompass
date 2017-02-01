<?php

include '../lib/lib.php';
opendb();

if(isset($_POST['try'])) {
  $try_id = $_POST['try']; 
  $query = "UPDATE `radios` set `trycnt` = `trycnt` + 1  WHERE `id` = $try_id";
  $result = mysql_query($query);
  if ($result) {
    $status = "OK";
  } else {
    $status = "failed";
  }
  $jsonarray =  array('status' => $status, 'data' => "");
  $output = json_encode($jsonarray, JSON_PRETTY_PRINT);
  echo $output;  
}

if(isset($_POST['play'])) {
  $play_id = $_POST['play'] ; 
  $query = "UPDATE `radios` set `playcnt` = `playcnt` + 1  WHERE `id` = $play_id";
  $result = mysql_query($query);
  if ($result) {
    $status = "OK";
  } else {
    $status = "failed";
  }
  $jsonarray =  array('status' => $status, 'data' => "");
  $output = json_encode($jsonarray, JSON_PRETTY_PRINT);
  echo $output;  
}

closedb();
?>
