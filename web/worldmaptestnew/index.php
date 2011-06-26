<?php
if(isset($_GET['x']) and is_numeric($_GET['x'])) {
  $x = $_GET['x'];
} else {
 $x = 0;
}
if(isset($_GET['y']) and is_numeric($_GET['y'])) {
  $y = $_GET['y'];
} else {
 $y = 0;
}
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
      <div class="location" style=<?php echo "\"left:${x}%;top:${y}%;\""; ?>>
        <img name="loeschi" id="mark" src="img/aussenden_anim0.png"  alt="o" />
      </div>
    </div>
  </div>
  <div class="clearfix">
  </div>
</div>

</body>
</html>
