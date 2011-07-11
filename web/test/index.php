<?php
include '../lib/lib.php';
opendb();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 
  <!-- jQuery fuer jplayer und dynamischen autorefresh -->
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
 
  <!-- jPlayer Zeugs -->
  <link href="skin/jplayer.blue.monday.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="js/jquery.jplayer.min.js"></script>
  <script type="text/javascript" src="js/rsk.stream.player.js"></script>
  <!-- jPlayer Zeugs -->

  <!-- style -->
  <link href="css/style.css" rel="stylesheet" type="text/css" />

  <!-- markierungsanimationsskript -->
  <script type="text/javascript" src="js/mark.animation.js"></script>

  <!-- Autorefresh Playlist & Markierung -->
  <script type="text/javascript">
  var d = new Date();
  var playlisturl = "playlist.php?offset="  + d.getTimezoneOffset();
  var auto_refresh = setInterval(
  function()
  {
  $('#playlist').load(playlisturl);
  $('#worldmap').load('markierung.php');
  }, 3000);
  </script>
  <!-- bis hier Autorefresh Playlist -->

  <!-- jscrollpane zeugs -->
  <script type="text/javascript" src="js/jquery.mousewheel.js"></script>
  <script type="text/javascript" src="js/jquery.jscrollpane.min.js"></script>
  <link type="text/css" href="css/jquery.jscrollpane.css" rel="stylesheet" media="all" />
  <script type="text/javascript" id="sourcecode">
    $(function()
    {
    $('.scrollpane').jScrollPane();
    });
  </script>

  <!-- Texboxen ein- und ausblenden -->
  <script type="text/javascript">
    function showstuff(boxid){
      document.getElementById(boxid).style.visibility="visible";
    }
    function hidestuff(boxid){
      document.getElementById(boxid).style.visibility="hidden";
    }
  </script>

  <title>RadioSolarKompass</title>
</head>

<body onload="timeMsg()">

<!-- container mit fester grösse, wo alles drin ist -->
<div id="main">

<!-- WELTKARTE -->
<div id="worldmap">
</div>
 
<!-- RSK LOGO -->
<div id="logo">
</div>
 
<!-- PLAYLIST -->
<div id="playlist">
</div>

<!-- MENU -->
<div id="menu">
<table class="menu">
  <tr>
    <td ><a class="menu" href="#" onclick="showstuff('menuabout');hidestuff('menuradiolist');">ABOUT</a></td>
  </tr>
  <tr>
    <td><a class="menu" href="#" onclick="showstuff('menuradiolist');hidestuff('menuabout');$('#radiolist').load('radiolist.php');">RADIO LIST</a></td>
  </tr>
  <tr>
    <td><a class="menu" href="http://www.radiosolarkompass.org/guestbook" target="_blank">GUESTBOOK</a></td>
  </tr>
</table>
</div> 

<!-- RSK PLAYER -->
<!-- jPlayer Code -->
<div id="player">
 <div id="jquery_jplayer"></div>
 <div class="jp-single-player"> 
        <div class="jp-interface">
                <ul class="jp-controls">
                        <li><a href="#" id="jplayer_play" class="jp-play" tabindex="1">play</a></li>
                        <li><a href="#" id="jplayer_pause" class="jp-pause" tabindex="1">pause</a></li>
                        <li><a href="#" id="jplayer_volume_min" class="jp-volume-min" tabindex="1">min volume</a></li>
                        <li><a href="#" id="jplayer_volume_max" class="jp-volume-max" tabindex="1">max volume</a></li>
                </ul>
                <div id="jplayer_volume_bar" class="jp-volume-bar">
                        <div id="jplayer_volume_bar_value" class="jp-volume-bar-value"></div>
                </div>
        </div>
 </div> 
</div>
<!-- jPlayer  bis hier -->

<!-- ABOUT TEXT -->
<div id="menuabout" class="popupbox">
<div class="close"><a href="#" onclick="hidestuff('menuabout');"><img src="pix/close.png"  border="0" alt="close" /></a></div>
<div class="scrollpane">
<h3>RadioSolarKompass</h3>
<p>
The radio composition of an invisible reality follows the sunrise: The programme behind RadioSolarKompass connects radios from all over the world depending on the time of sunrise. You can see on the worldmap, where actually the sun is rising and from this location, you can hear a radio broadcast. 
The collection consists of 1500 radio stations and the density is not constant.  The times of changes between the radio stations will be of longer and shorter duration.
RadioSolarKompass can also be interpreted as sonification of the rotation of our earth. <br />
<p/>
<h3>Pilosophy</h3>
<p>
Space. We can feel the impact of the connection to the macrocosmos, when we watch the stars. The extraordinarily beautiful moment of sunrise evokes a concentrated reference to the sun within the magic ofcosmic space. RadioSolarKompass aims to be a compass to stimulate sense and imagination, a planetary feeling for the space around us.
Space as a metaphor for freedom: We embrace the fundamental changes, which the Internet has brought by multiplying media channels and by increasing the possibilities for a wider spectrum of broadcasts and information.
</p>
</div>
</div>

<!-- RADIOLIST -->
<div id="menuradiolist" class="popupbox">
<div class="close"><a href="#" onclick="hidestuff('menuradiolist');"><img src="pix/close.png" border="0" alt="close" /></a></div>
<div class="scrollpane" id="radiolist">
<h2>......L O A D I N G .........</h2>
<?php //displayRadioList(); ?>
</div>
</div>


</div>

</body>
</html>
<?php closedb(); ?>
