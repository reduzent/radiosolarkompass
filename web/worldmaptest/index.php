<?php
include '../lib/lib.php';
opendb();

$query = "
     select
       `radios`.`name`, 
       `radios`.`homepage`, 
       `radios`.`url`, 
       `cities`.`name`,
       X(`cities`.`coord`),
       Y(`cities`.`coord`),
       `countries`.`name` 
    from `status` 
    left join `radios` on 
      `radios`.`id` = `status`.`value` 
    join `cities` on 
      `cities`.`id` = `radios`.`city_id`
    join `countries` on
      `countries`.`iso` = `cities`.`country_code`
    where `status`.`param` = 'onair'
    ";
$result = mysql_query($query) or die ('Datenbank-Abfrage fehlgeschlagen');
list($name, $homepage, $url, $city, $lat, $lon, $country) = mysql_fetch_array($result);



/*
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
*/

$x = floor($lon * 2.29333 + 403.116 - 15 + 0.5);
$y = floor($lat * -2.30868 + 222.567 - 15 + 0.5);

closedb();
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript">
  var delay = 100;
  var num = 5;
  var current = 0;
  var urlbase = 'img/aussenden_anim';
  function timeMsg()
  {
    var t=setTimeout("timeMsg()",delay);
    current = (current + 1) % num;
    loeschi.src = urlbase + current + '.png';
  }
  timeMsg();
</script>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" media="all" type="text/css" href="style.css" />
<title>World Map Test</title>
</head>
<body>
<h4>The world in 850 x 410 pixels</h4>
<?php
echo "<p>Latitude: $lat<br/>Longitude: $lon</p>\n";
?>
<div id="worldmap">
  <div id="location" style="left:<?php echo $x;?>px;top:<?php echo $y;?>px;">
     <img name="loeschi" src="img/aussenden_anim0.png"  alt="o" />
  </div>
</div>
<?php
$url_short = truncateUrl($url);
echo "<div class='fig'><a href='$homepage'>$name</a></div>\n";
echo "<div class='fig'><a href='$url'>$url_short</a></div>\n";
echo "<div class='fig'>$city</div>\n";
echo "<div class='fig'>$country</div>\n";
?>
</body>
</html>
