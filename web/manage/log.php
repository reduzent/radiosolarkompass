<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
// init db connection
include '../lib/lib.php';
opendb();

$datum = getdate();
$today =  $datum['year'] . "-" . twoDigit($datum['mon']) . "-" . twoDigit($datum['mday']);
if (isset($_GET['date'])) {
  $selected_date = $_GET['date'];
} else {
  $selected_date = $today;
}

function generateLogDateSelector() {
  $query = "select 
     substring(`onair_time`, 1, 10) 
     as `date` 
     from `onair_log` 
     group by `date` 
     order by `date` desc
  ";
  global $today;
  global $selected_date;
  $result = mysql_query($query) or die ('Datenbankabfrage fehlgeschlagen');
  echo "<select name=\"date\" onChange=\"this.form.submit()\" id=\"logdate\">\n";
  echo "  <option value=\"$today\">today</option>\n";
  while(list($date) = mysql_fetch_array($result)) {
    if ( $date == $selected_date ) {
      echo "  <option selected value=\"$date\">$date</option>\n";
    } else {
      echo "  <option value=\"$date\">$date</option>\n";
    }
  }
  echo "</select>\n";
}
 
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" media="all" type="text/css" href="style.css" />
<title>Manage RSK Database</title>
</head>
<body>
<?php switcher(); ?>
<h4>Log from <?php echo $selected_date; ?></h4>
<form name="show_log" action="log.php" method="get">
Select Date: 
<?php generateLogDateSelector(); ?>
</form><br />
<?php displayLog($selected_date); ?>
</body>
</html>
<?php 
closedb();
?>
