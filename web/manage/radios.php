<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
// init db connection
include '../lib/lib.php';
opendb();

// Change active state according to selection
if (isset($_POST['active'])) {
  foreach ($_POST['active'] as $tochange) {
    $query = "select `active` from `radios` where `id` = $tochange";
    $result = mysqli_query($conn, $query) or die ('Datenbank-Fehler');
    list($active) = mysql_fetch_array($result);
    $active = fmod($active + 1, 2);
    $query = "update `radios` set `active` = $active where `id` = $tochange";
    mysqli_query($conn, $query) or die ('Datenbank-Fehler');
  }
}

// Delete the ones with 'delete' checked
if (isset($_POST['delete'])) {
  foreach ($_POST['delete'] as $todelete) {
    $query = "delete from `radios` where `id` = $todelete";
    mysqli_query($conn, $query) or die ('Datenbank-Fehler');
  }
}

// generate title numbers
$query = "select count(*) from radios where operable = true";
$result = mysqli_query($conn, $query) or die('Datenbankabfrage fehlgeschlagen');
list($total) = mysqli_fetch_array($result);
$query = "select count(*) from radios where active = true and operable = true";
$result = mysqli_query($conn, $query) or die('Datenbankabfrage fehlgeschlagen');
list($active) = mysqli_fetch_array($result);
mysqli_query($conn, "select SQL_CALC_FOUND_ROWS city_id from radios where operable = 1 group by city_id;");
$query = "select FOUND_ROWS();";
$result = mysqli_query($conn, $query) or die('Datenbankabfrage fehlgeschlagen');
list($number_of_cities) = mysqli_fetch_array($result);

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" media="all" type="text/css" href="style.css"/>
<title>Manage RSK Database</title>
</head>
<body>
<?php switcher(); ?>
<h4>Manage Radio Stations <?php echo "($active/$total)"; ?> from <?php echo "$number_of_cities"; ?> locations</h4>
<form name="manage_radios" action="radios.php" method="post">
<?php
displayStreamList();
?>
</form>
</body>
</html>
<?php
// close db connection
closedb();
?>
