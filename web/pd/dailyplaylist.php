<?php
include '../lib/lib.php';
opendb();

generateDailySchedule();
// close db connection
closedb();
?>

