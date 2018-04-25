<?php

include '../lib/lib.php';
opendb();

$status = "failed";
if(isset($_POST['try'])) {
  $try_id = $_POST['try']; 
  $query = "UPDATE `radios` set `trycnt` = `trycnt` + 1  WHERE `id` = $try_id";
  $result = mysqli_query($conn, $query);
  if (mysqli_affected_rows($conn) == 1) {
    $status = "OK";
  }
} elseif (isset($_POST['play'])) {
  $play_id = $_POST['play'] ; 
  $query = "UPDATE `radios` set `playcnt` = `playcnt` + 1  WHERE `id` = $play_id";
  $result = mysqli_query($conn, $query);
  if (mysqli_affected_rows($conn) == 1) {
    $status = "OK";
  }
}

$jsonarray =  array('status' => $status, 'data' => "");
$output = json_encode($jsonarray, JSON_PRETTY_PRINT);
echo $output;  

closedb();
?>
