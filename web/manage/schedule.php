<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
// init db connection
include '../lib/lib.php';
opendb();

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" media="all" type="text/css" href="style.css" />
<title>Manage RSK Database</title>
</head>
<body>
<?php switcher(); ?>
<h4>Sunrises Around The World (<?php currentDate() ?>)</h4>
<?php generateSunriseSchedule(); ?>
</body>
</html>
<?php 
closedb();
?>
