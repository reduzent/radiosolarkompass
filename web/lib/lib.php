<?php
include 'config.php';

function opendb() {
  global $dbhost;
  global $dbuser;
  global $dbpass;
  global $dbname;
  global $conn;
  $conn = mysql_connect($dbhost, $dbuser, $dbpass) or die ('verbindung mit datenbank fehlgeschlagen');
  mysql_select_db($dbname);
}

function closedb() {
  global $conn;
  mysql_close($conn);
}

function switcher() {
  $links = array(
    'index'       => 'Add new entries',
    'radios'      => 'Manage radio stations',
    'locations'   => 'Manage locations',
    'schedule'    => 'Schedule'
  );
  foreach($links as $page => $text) {
    if ($_SERVER['PHP_SELF'] == "/manage/$page.php") {
      echo "<div class=\"menu\" id=\"current\">\n";
      echo "$text\n";
    } else {
      echo "<div class=\"menu\">\n";
      echo "<a class=\"menu\" href=\"/manage/$page.php\">$text</a>\n";
    }
    echo "</div>\n";
  }
}

function calcSunriseTime($lat, $lon, $tz) {
  $B = $lat / 57.295779512;             // Breite im Bogenmass
  $L = $lon / 57.295779512;             // Laenge im Bogenmass
  $Zeitzone = $tz;                      // Zeitzone: 0 = GMT
  $ref = mktime(0, 0, 0, 1, 1, 2000);   // unix time von 2000-01-01, 00:00:00
  $h = -0.0145;                         // erforderlich Sonnenhöhe im Bogenmass;
  $now = time();                        // Jetzt in Unix time
  //$now = mktime(0, 0, 0, 4, 19, 2011);
  $delta_seconds = $now - $ref;
  // Tage seit 2000-01-01
  $delta_days = floor($delta_seconds / 60 / 60 / 24);
  $T = $delta_days / 36525;
  $M = 2 * M_PI * (0.993133 + 99.997361 * $T);
  $M = fmod($M, 2 * M_PI);
  $Lweg = 2 * M_PI * (0.7859453 + ( $M / ( 2 * M_PI)) + (6893.0 * sin($M) + 72.0 * sin (2.0 * $M) + 6191.2 * $T ) / 1296000 );
  $Lweg = fmod($Lweg, 2 * M_PI);
  $e = 2 * M_PI * (23.43929111 + (-46.8150 * $T - 0.00059 * $T * $T + 0.001813 * $T * $T * $T) / 3600.0) / 360;
  $DK = asin(sin($e) * sin($Lweg));
  $RA = atan(tan($Lweg) * cos($e));
  if ( $RA < 0 ) {
    $RA += M_PI;
  }
  if ( $Lweg > M_PI ) {
    $RA += M_PI;
  }
  $RAm = 18.71506921 + 2400.0513369 * $T + (0.000025862 - 0.00000000172 * $T) * $T * $T;
  $RAm = fmod($RAm, 24);
  $Zeitgleichung = 1.0027379 * ( $RAm - 3.81972 * $RA);
  $Zeitdifferenz = 12 * acos((sin($h) - sin($B)*sin($DK))/(cos($B) * cos($DK))) / M_PI;
  $Aufgang_OZ = 12 - $Zeitdifferenz - $Zeitgleichung; //-  3.81972 * fmod($L, (M_PI / 12));
  $Aufgang = fmod(($Aufgang_OZ - ( $L * 57.295779512 / 15 ) + $Zeitzone) + 24, 24);
  return $Aufgang;
}

function twoDigit($i) {
  $padded = str_pad("$i", 2, '0', STR_PAD_LEFT);
  return $padded;
}

function currentDate() {
  $datum = getdate();
  $print =  $datum['year'] . "-" . twoDigit($datum['mon']) . "-" . twoDigit($datum['mday']);
  echo $print;
}

function convertTdecThms($tdec) {
  $h = floor($tdec);
  $m = floor(fmod($tdec, 1) * 60);
  $s = fmod(floor(fmod($tdec, 1) * 3600), 60);
  $thms = twoDigit($h) . ":" . twoDigit($m) . ":" . twoDigit($s);
  return $thms;
}

function pdGetStreamsOfLocation($location_id) {
  $query = "select `id`, `url` from `radios` where `location_id` = $location_id";
  $result = mysql_query($query) or die ('Datenbank-Abfrage fehlgeschlagen');
  while(list($id, $url) = mysql_fetch_array($result)) {
    echo "$id $url;\n";
  }
}

function generatePdPlaylist() {
  $query = 'select `id`, `city`, `country`, X(coord), Y(coord) from `locations`';
  $result = mysql_query($query) or die ('Datenbank-Abfrage fehlgeschlagen');
  $timetable = array();
  while(list($id, $city, $country, $lat, $lon) = mysql_fetch_array($result)) {
    $sunrise_dec = calcSunriseTime($lat, $lon, 0);
    $timetable[] = array($sunrise_dec, $id);
    sort($timetable);
  }
  foreach($timetable as $row)  {
    list($sunrise_dec, $id) = $row;
    $h = floor($sunrise_dec);
    $m = floor(fmod($sunrise_dec, 1) * 60);
    $s = fmod(floor(fmod($sunrise_dec, 1) * 3600), 60);
    echo "$h $m $s $id;\n";
  }
}

function generateSunriseSchedule () {
  $query = 'select `id`, `city`, `country`, X(coord), Y(coord) from `locations`';
  $result = mysql_query($query) or die ('Datenbank-Abfrage fehlgeschlagen');
  $timetable = array();
  while(list($id, $city, $country, $lat, $lon) = mysql_fetch_array($result)) {
    $sunrise_dec = calcSunriseTime($lat, $lon, 0);
    $timetable[] = array($sunrise_dec, $city, $country);
    sort($timetable);
  }
?>
<table>
<tr>
  <th>SUNRISE</th>
  <th>CITY</th>
  <th>COUNTRY</th>
</tr>
<?php
  $bgclr = 0;
  foreach($timetable as $row)  {
    list($sunrise_dec, $city, $country) = $row;
    echo "<tr class=\"bg$bgclr\">\n";
    $bgclr += 1;
    $bgclr = fmod($bgclr, 2);
    $sunrise = convertTdecThms($sunrise_dec);
    echo "  <td class=\"numeric\">$sunrise</td>\n";
    echo "  <td>$city</td>\n";
    echo "  <td>$country</td>\n";
    echo "</tr>";
  }
  echo "</table>\n";
}



//###### index.php functions ##########################
// Validate POST data
function validate_lat ($lat) {
  global $location_id;
  global $lon_global;
  $lon = $lon_global;
  $valid = true;
  if ($lat == '0.0' and $lon == '0.0') {
    $valid = false;
  }
  if ( -90 < $lat and $lat < 90 and is_numeric($lat) and $valid or $location_id > 0) {
    return true;
  } else {
    return false;
  }
}

function validate_lon ($lon) {
  global $location_id;
  global $lat_global;
  $lat = $lat_global;
  $valid = true;
  if ($lat == '0.0' and $lon == '0.0') {
    $valid = false;
  }
  if ( -180 < $lon and $lon < 180 and is_numeric($lon) and $valid or $location_id > 0) {
    return true;
  } else {
    return false;
  }
}

function validate_city ($city) {
  if ( $city != "" ) {
    return true;
  } else {
    return false;
  }
}

function validate_country ($country) {
  if ( $country != "empty" ) {
    return true;
  } else {
    return false;
  }
}

function validate_radio ($radio) {
  if ( $radio != "" ) {
    return true;
  } else {
    return false;
  }
}

function validate_url ($url) {
  return true;
 /* if (filter_var($url, FILTER_VALIDATE_URL)) {
    return true;
  } else {
    return false;
  }
 */
}

function validate_homepage ($url) {
  if (filter_var($url, FILTER_VALIDATE_URL) or $url == '') {
    return true;
  } else {
    return false;
  }
}

// other functions
function generateCountrySelector ($selected) {
  $start = <<<EOH
  <select id="country" name="country">
  <option value="empty">Select a country:</option>

EOH;
  echo($start);
  global $countries;
  foreach ($countries as $countrydata) {
    $country = $countrydata[0];
    $country_id = $countrydata[1];
    if ($country == $selected) {
      echo "<option value=\"$country_id\" selected>$country</option>\n";
    } else {
      $country_text = htmlspecialchars($country);
      echo "<option value=\"$country_id\">$country_text</option>\n";
    }
  }
  echo "</select>\n";
}

function generateCitySelectors($sel_country_id) {
  global $countries;
  global $emptyness;
  global $allok;
  $emptyness = "empty";
  foreach ($countries as $countrydata) {
    $country = $countrydata[0];
    $country_id = $countrydata[1];
    $query = "SELECT `id`, `city` FROM `locations` where `country` = '$country' order by `city`";
    $cities = mysql_query($query) or die ('Datenbankabfrage fehlgeschlagen');
    if ($sel_country_id == $country_id and mysql_num_rows($cities) > 0 and $allok == false) {
      echo "<select class=\"visible\" name=\"cities-$country_id\">\n";
      $emptyness = "populated";
    } else {
      echo "<select name=\"cities-$country_id\">\n";
    }
    $something = false;
    $poststring = "cities-" . $country_id;
    while(list($city_id, $city) = mysql_fetch_array($cities)) {
      if (isset($_POST[$poststring]) and $_POST[$poststring] == $city_id) {
        echo "  <option selected value=\"$city_id\">$city</option>\n";
      } else {
        echo "  <option value=\"$city_id\">$city</option>\n";
      }
      $something = true;
    }
  //  if ($something) { 
      if (isset($_POST[$poststring]) and $_POST[$poststring] == "new" and $allok == false ) {
       echo "  <option selected value=\"new\">Other...</option>\n";
      } else {
       echo "  <option value=\"new\">Other...</option>\n";
      }
   // }
    echo "</select>\n";
  }
}

//######## location.php functions #######################################
function displayCityList() {
  $query = 'select `id`, `city`, `country`, X(coord), Y(coord) from `locations` order by `country`, `city`';
  $result = mysql_query($query) or die ('Datenbank-Abfrage fehlgeschlagen');
?>
<table id="streamlist">
<tr>
  <th>CITY</th>
  <th>COUNTRY</th>
  <th>LATITUDE</th>
  <th>LONGITUDE</th>
  <th id="dangerous">delete</th>
</tr>
<?php
  $bgclr = 0;
  while(list($id, $city, $country, $lat, $lon) = mysql_fetch_array($result)) {
    echo "<tr class=\"bg$bgclr\">\n";
    $bgclr += 1;
    $bgclr = fmod($bgclr, 2);
    echo "  <td>$city</td>\n";
    echo "  <td>$country</td>\n";
    echo "  <td class=\"numeric\">$lat</td>\n";
    echo "  <td class=\"numeric\">$lon</td>\n";
    echo "  <td class=\"delete\"><input class=\"delete\" type=\"checkbox\" name=\"delete[]\" value=\"$id\" /></td>\n";
    echo "</tr>\n";
  }
  echo "</table>\n";
  echo "<input class=\"button\" type=\"submit\" value=\"Delete\" />";
}

//########## radios.php functions ###########################################
function truncateUrl($url) {
  $urlvis = substr($url, 0, 40);
  if (strlen($urlvis) < strlen($url) ) {
    $urlvis .= "...";
  }
  return $urlvis;
}

function displayStreamList() {
  $query = 'select `radios`.`active`, `radios`.`id`, `radios`.`name`, `radios`.`homepage`, `radios`.`url`, `locations`.`city`, `locations`.`country` from `radios`, `locations` where (`radios`.`location_id` = `locations`.`id`) order by `locations`.`country`, `locations`.`city`';
  $result = mysql_query($query) or die ('Datenbank-Abfrage fehlgeschlagen');
?>
<table id="streamlist">
<tr>
  <th id="hide"></th>
  <th>STATION</th>
  <th>URL</th>
  <th>CITY</th>
  <th>COUNTRY</th>
  <th id="dangerous">delete</th>
</tr>
<?php
  $bgclr = 0;
  while(list($active, $id, $name, $homepage, $url, $city, $country) = mysql_fetch_array($result)) {
    echo "<tr class=\"bg$bgclr\">\n";
    $bgclr += 1;
    $bgclr = fmod($bgclr, 2);
    $class = "inactive";
    $state = 'off';
    if ($active == 1) {
      $class = "active";
      $state = 'on';
    }
    echo "  <td class=\"$class\"><input class=\"active\" type=\"checkbox\" name=\"active[]\" value=\"$id\" />$state</td>\n";
    if ($homepage == '') {
      echo "  <td>$name</td>\n";
    } else {
      echo "  <td><a href=\"$homepage\">$name</a></td>\n";
    }
    echo "  <td><a href=\"$url\">" . truncateUrl($url) . "</a></td>\n";
    echo "  <td>$city</td>\n";
    echo "  <td> $country</td>\n";
    echo "  <td class=\"delete\"><input class=\"delete\" type=\"checkbox\" name=\"delete[]\" value=\"$id\" /></td>\n";
    echo "</tr>\n";
  }
  echo "</table>\n";
  echo "<input class=\"button\" type=\"submit\" value=\"Update / Delete\" />";
}


?>
