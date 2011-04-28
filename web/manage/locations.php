<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
// init db connection
include '../lib/lib.php';
opendb();

// Delete the ones with 'delete' checked
$errorlist = '';
if (isset($_POST['delete'])) {
  foreach ($_POST['delete'] as $todelete) {
    $query = "select `name` from `radios` where `location_id` = $todelete";
    $result = mysql_query($query) or die ('Datenbank-Abfrage fehlgeschlagen');
    if (mysql_num_rows($result) == 0) {
      $query = "delete from `locations` where `id` = $todelete";
      mysql_query($query) or die ('Datenbank-Fehler');
    } else {
      while(list($radio) = mysql_fetch_array($result)) {
        $errorlist .= "<li>$radio</li>\n";
      }
    }
  }
}

// Generate error message
$errormsg = '';
if ($errorlist != '') {
  $errormsg = "<h3>ERROR</h3>\nThe following radio stations are still associated with the selected locations:\n<ul>\n" . $errorlist . "</ul>\n";
}

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" media="all" type="text/css" href="style.css" />
<title>Manage RSK Database</title>
</head>
<body>
<?php
switcher(); 
echo $errormsg; ?>

<h4>Manage Locations</h4>
<form name="manage_locations" action="locations.php" method="post">
<?php
displayCityList();
?>
</form>
</body>
</html>
<?php
// close db connection
closedb();
?>
