<?php
include '../lib/lib.php';
opendb();
$status = "failed";
if(isset($_POST['onair'])) {
  $id = $_POST['onair'];
  // move old 'onair' to 'played' 
  $query = "
    update status s1, status s2 
    set s1.value = s2.value,
        s1.playtime = s2.playtime
    where s1.param = 'played' 
      and s2.param = 'onair'
     ";
  $result = mysql_query($query);
  if ($result) {
    // update current 'onair' status
    $query = "
      UPDATE `status` 
      SET
        `value` = $id,
        `playtime` = now()
      WHERE `param` = 'onair'";
    mysql_query($query) and $status = "OK";
    // write entry to log
    $query = "
      insert into `onair_log` 
      (`radio_id`, `onair_time`) 
      values 
      ($id, (
        select playtime
        from status 
        where param = 'onair'
        )
      )";
    mysql_query($query);
  }
} elseif (isset($_POST['next'])) {
  $id = $_POST['next']; 
  $query = "
    UPDATE `status` 
    SET 
      `value` = $id,
      `playtime` = (
        select ds.sunrise_time
        from daily_schedule ds 
        join radios r on r.city_id = ds.city_id 
        where r.id = $id
        )
    WHERE `param` = 'next'";
  mysql_query($query) and $status = "OK";
} elseif (isset($_POST['online'])) {
  $id = $_POST['online']; 
  $query = "
    UPDATE `status` 
    SET
      `value` = $id,
      `playtime` = now() 
    WHERE `param` = 'online'";
  mysql_query($query) and $status = "OK";
}

$jsonarray =  array('status' => $status, 'data' => "");
$output = json_encode($jsonarray, JSON_PRETTY_PRINT);
echo $output;

closedb();
?>
