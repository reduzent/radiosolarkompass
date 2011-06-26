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

$x_perc = $lon / 3.58494 + 46.9449;
$y_perc = rad2deg(0.5 * log((1 + sin(deg2rad($lat)))/(1 - sin(deg2rad($lat))))) / -2.81713 + 59.3701;

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
</script>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" media="all" type="text/css" href="style.css" />
<title>World Map Test</title>
</head>
<body onload="timeMsg()">
<h4>The world dynamically scaled</h4>

<div class="worldmap">
  <div>
    <img src="img/worldmap_new_2942x2312.png" />
    <div>
      <div class="location" style=<?php echo "\"left:${x_perc}%;top:${y_perc}%;\""; ?>>
        <img name="loeschi" id="mark" src="img/aussenden_anim0.png"  alt="o" />
      </div>
    </div>
  </div>
  <div class="clearfix">
  </div>
</div>

</body>
</html>
