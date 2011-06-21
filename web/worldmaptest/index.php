<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
if(isset($_GET['lat']) and is_numeric($_GET['lat'])) {
  $lat = $_GET['lat'];
} else {
  $lat = 0;
} 
if(isset($_GET['lon']) and is_numeric($_GET['lon'])) {
  $lon = $_GET['lon'];
} else {
  $lon = 0;
}

$x = floor($lon * 2.29333 + 403.116 - 1 + 0.5);
$y = floor($lat * -2.30868 + 222.567 - 1 + 0.5);

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" media="all" type="text/css" href="style.css"/>
<title>World Map Test</title>
</head>
<body>
<h4>This is the world in 850 x 410 pixels</h4>
<?php
echo "<p>Latitude: $lat<br/>Longitude: $lon</p>\n";
?>
<div id="worldmap">
  <div id="location" style="left:<?php echo $x;?>px;top:<?php echo $y;?>px;">
    <div id="point"></div>
  </div>
</div>
</body>
</html>
