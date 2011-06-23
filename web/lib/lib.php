<?php
include 'config.php';

function opendb() {
  global $dbhost;
  global $dbuser;
  global $dbpass;
  global $dbname;
  global $conn;
  $conn = mysql_connect($dbhost, $dbuser, $dbpass) or die ('Verbindung mit Datenbank fehlgeschlagen');
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
    //'locations'   => 'Manage locations',
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

function pdGetStreamsOfLocation($city_id) {
  $query = "select `id`, `url` from `radios` where `city_id` = $city_id";
  $result = mysql_query($query) or header('HTTP/1.0 500 Internal Server Error');
  while(list($id, $url) = mysql_fetch_array($result)) {
    echo "$id $url;\n";
  }
}

function pdGetAnnounceData($radio_id) {
  $query = "
    select 
      `cities`.`name`, 
      `countries`.`name`, 
      `radios`.`name`
    from `radios`
    join `cities` 
    on `radios`.`city_id` = `cities`.`id`
    join `countries` 
    on `cities`.`country_code` = `countries`.`iso` 
    where `radios`.`id` = '$radio_id'";
  $result = mysql_query($query) or header('HTTP/1.0 500 Internal Server Error');
  while(list($city, $country, $radio) = mysql_fetch_array($result)) {
    $city_clean = normalize_special_characters($city);
    $country_clean = normalize_special_characters($country);
    $radio_clean = normalize_special_characters($radio);
    echo "city $city_clean;\n";
    echo "country $country_clean;\n";
    echo "radio $radio_clean;\n";
  }
}

function generatePdPlaylist() {
  $query = "
    select 
    `cities`.`id`,
    X(`cities`.`coord`), 
    Y(`cities`.`coord`) 
    from `cities`
    join `radios`
    on `radios`.`city_id` = `cities`.`id`
    where  `radios`.`active` = true
    group by `cities`.`id`;
    ";
  $result = mysql_query($query) or header('HTTP/1.0 500 Internal Server Error');
  $timetable = array();
  while(list($id, $lat, $lon) = mysql_fetch_array($result)) {
    $sunrise_dec = calcSunriseTime($lat, $lon, 0);
    if (is_nan($sunrise_dec) == false) {
      $timetable[] = array($sunrise_dec, $id);
    }
    sort($timetable);
  }
  foreach($timetable as $row)  {
    list($sunrise_dec, $id) = $row;
    $h = floor($sunrise_dec);
    $m = floor(fmod($sunrise_dec, 1) * 60);
    $s = fmod(floor(fmod($sunrise_dec, 1) * 3600), 60);
    echo "$h $m $s _$id;\n";
  }
}

function generateSunriseSchedule () {
  $query = "
    select 
    count(*),
    `cities`.`id`, 
    `cities`.`name`, 
    `admin_codes`.`name`,
    `countries`.`name`, 
    X(`cities`.`coord`), 
    Y(`cities`.`coord`) 
    from `cities`
    left join `countries`
    on `cities`.`country_code` = `countries`.`iso`
    left join `admin_codes`
    on `admin_codes`.`code` = concat(`cities`.`country_code`, '.', `cities`.`admin1_code`)
    right join `radios`
    on `radios`.`city_id` = `cities`.`id`
    group by `radios`.`city_id`
    ";
  $result = mysql_query($query) or die ('Datenbank-Abfrage fehlgeschlagen');
  $timetable = array();
  while(list($count, $id, $city, $region, $country, $lat, $lon) = mysql_fetch_array($result)) {
    $sunrise_dec = calcSunriseTime($lat, $lon, 0);
    if (is_nan($sunrise_dec) == false) {
      $timetable[] = array($sunrise_dec, $count, $city, $region, $country);
    }
    sort($timetable);
  }
?>
<table>
<tr>
  <th>SUNRISE</th>
  <th>CITY</th>
  <th>REGION</th>
  <th>COUNTRY</th>
  <th>RADIO COUNT</th>
</tr>
<?php
  $bgclr = 0;
  foreach($timetable as $row)  {
    list($sunrise_dec, $count, $city, $region, $country) = $row;
    echo "<tr class=\"bg$bgclr\">\n";
    $bgclr += 1;
    $bgclr = fmod($bgclr, 2);
    $sunrise = convertTdecThms($sunrise_dec);
    echo "  <td class=\"numeric\">$sunrise</td>\n";
    echo "  <td>$city</td>\n";
    echo "  <td>$region</td>\n";
    echo "  <td>$country</td>\n";
    echo "  <td class=\"numeric\">$count</td>\n";
    echo "</tr>";
  }
  echo "</table>\n";
}



//###### index.php functions ##########################
// Validate POST data

/*
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
*/

function validate_city ($city) {
  global $warnings;
  global $country;
  $warnings['city'] = '';
  if ( $city != "empty" ) {
    return true;
  } else {
    if ( $country != 'empty' ) {
      $warnings['city'] = '<-- Please select a city';
    }
    return false;
  }
}

function validate_country ($country) {
  global $warnings;
  $warnings['country'] = '';
  if ( $country != "empty" ) {
    return true;
  } else {
    $warnings['country'] = '<-- Please select a country';
    return false;
  }
}

function validate_radio ($radio) {
  global $warnings;
  if ( $radio != "" ) {
    $warnings['radio'] = '';
    return true;
  } else {
    $warnings['radio'] = '<-- Please enter something';
    return false;
  }
}

function validate_url ($url) {
  global $warnings;
  if ($url == "http://") {
    $warnings['url'] = '<-- Please enter something';
    return false;
  } else {
    $warnings['url'] = '';
    return true;
  }
 /* if (filter_var($url, FILTER_VALIDATE_URL)) {
    return true;
  } else {
    return false;
  }
 */
}

function validate_homepage ($url) {
  global $warnings;
  $warnings['homepage'] = '';
  if (filter_var($url, FILTER_VALIDATE_URL) or $url == '') {
    return true;
  } elseif ($url == "http://") {
    $warnings['homepage'] = '<-- Please enter a URL';
    return false;
  } else {
    $warnings['homepage'] = '<-- Please enter a valid URL';
    return false;
  }
}

// other functions
function warn($type) {
  global $warnings;
  global $complete;
  if ( $complete ) {
    echo $warnings[$type];
  }
}

function generateCountrySelector() {
  global $country;
  $query = "SELECT `iso`, `name` FROM `countries` ORDER by `name`";
  $countriessql = mysql_query($query) or die ('Datenbankabfrage fehlgeschlagen');
  echo "<select name=\"country\" onChange=\"this.form.submit()\" id=\"country\">\n";
  echo "  <option value=\"empty\">Select Country...</option>\n";
  while(list($iso, $countrylocal) = mysql_fetch_array($countriessql)) {
    if ( $country == $iso ) {
      echo "  <option selected value=\"$iso\">$countrylocal</option>\n";
    } else {
      echo "  <option value=\"$iso\">$countrylocal</option>\n";
    }
  }
  echo "</select>\n";
}

function generateCitySelector() {
  global $latitude;
  global $longitude;
  global $country;
  global $city;
  $latitude = "";
  $longitude = "";
  if ( $country != "empty" ) {
    $cc = $country;
    $query = "SELECT `id`, `name`, `admin1_code`, X(coord), Y(coord) FROM `cities` where `country_code` = '$cc' ORDER by `name`";
    $citiessql = mysql_query($query) or die ('Datenbankabfrage fehlgeschlagen');
    echo "<select name=\"city\" onChange=\"this.form.submit()\" id=\"city\">\n";
    echo "  <option value=\"empty\">Select a City...</option>\n";
    while(list($id, $citylocal, $division, $lat, $long) = mysql_fetch_array($citiessql)) {
      if ( $city == $id ) {
        echo "  <option selected value=\"$id\">$citylocal ($division)</option>\n";
        $latitude = $lat;
        $longitude = $long;
      } else {
        echo "  <option value=\"$id\">$citylocal ($division)</option>\n";
      }
    }
    echo "</select>\n";
  }
}

function addShowNewEntry() {
  global $todb;
  global $allok;
  if ( $allok ) {
    echo "<h4>Added the following data to the database</h4>\n";
    echo "<table> \n";
    foreach ($todb as $type => $value) {
      echo "<tr>\n";
      echo "<td>$type:</td>\n";
      echo "<td class=\"highlight\">$value</td>\n";
      echo "</tr>\n";
    }
    echo "</table>\n";
    $query = "
      insert into `radios` 
      (`name`, `homepage`, `url`, `city_id`)
      values
      ('${todb['radio']}', '${todb['homepage']}', '${todb['url']}', '${todb['city']}');";
    mysql_query($query) or die ('Datenbankabfrage fehlgeschlagen');
  }
}

//######## location.php functions #######################################
/*function displayCityList() {
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
*/

//########## radios.php functions ###########################################
function truncateUrl($url) {
  $urlvis = substr($url, 0, 40);
  if (strlen($urlvis) < strlen($url) ) {
    $urlvis .= "...";
  }
  return $urlvis;
}

function displayStreamList() {
  $query = '
     select 
       `radios`.`active`, 
       `radios`.`id`, 
       `radios`.`name`, 
       `radios`.`homepage`, 
       `radios`.`url`, 
       `cities`.`name`, 
       `countries`.`name` 
    from `radios` 
    left join `cities` on 
      `cities`.`id` = `radios`.`city_id` 
    left join `countries` on 
      `countries`.`iso` = `cities`.`country_code` 
    order by `countries`.`name`,
      `cities`.`name`,
      `radios`.`name`
    ';
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

// ############ whatsup.php functions ################################################################

function displayWhatsupList() {
  $query = '
     select
       `status`.`param`,
       `status`.`mod_time`, 
       `radios`.`name`, 
       `radios`.`homepage`, 
       `radios`.`url`, 
       `cities`.`name`, 
       `countries`.`name` 
    from `status` 
    left join `radios` on 
      `radios`.`id` = `status`.`value` 
    inner join `cities` on 
      `cities`.`id` = `radios`.`city_id`
    inner join `countries` on
      `countries`.`iso` = `cities`.`country_code`
    order by `status`.`mod_time`
    ';
  $result = mysql_query($query) or die ('Datenbank-Abfrage fehlgeschlagen');
?>
<table id="streamlist">
<tr>
  <th id="hide"></th>
  <th>TIME</th>
  <th>RADIO</th>
  <th>URL</th>
  <th>CITY</th>
  <th>COUNTRY</th>
</tr>
<?php
  $bgclr = 0;
  while(list($role, $time, $name, $homepage, $url, $city, $country) = mysql_fetch_array($result)) {
    echo "<tr class=\"bg$bgclr\">\n";
    $bgclr += 1;
    $bgclr = fmod($bgclr, 2);
    echo " <td>$role</td>\n";
    echo " <td>$time</td>\n";
    if ($homepage == '') {
      echo "  <td>$name</td>\n";
    } else {
      echo "  <td><a href=\"$homepage\">$name</a></td>\n";
    }
    echo "  <td><a href=\"$url\">" . truncateUrl($url) . "</a></td>\n";
    echo "  <td>$city</td>\n";
    echo "  <td> $country</td>\n";
    echo "</tr>\n";
  }
  echo "</table>\n";
}


// ############ import.php functions #################################################################


function displayImportList() {
  $query = '
     select 
       `id`,
       `name`,
       `homepage`,
       `url`, 
       `city`,
       `country`  
    from `radios_import` 
    where `imported` = false and `url_valid` = true
    order by `country`, `city`, `name`
    ';
  $result = mysql_query($query) or die ('Datenbank-Abfrage fehlgeschlagen');
?>
<table id="streamlist">
<tr>
  <th id="hide"></th>
  <th>STATION</th>
  <th>URL</th>
  <th>CITY</th>
  <th>COUNTRY</th>
</tr>
<?php
  $bgclr = 0;
  while(list($id, $name, $homepage, $url, $city, $country) = mysql_fetch_array($result)) {
    echo "<tr class=\"bg$bgclr\">\n";
    $bgclr += 1;
    $bgclr = fmod($bgclr, 2);
    echo "  <td class=\"$class\"><input class=\"button\" type=\"button\" name=\"active[]\" value=\"$id\" /></td>\n";
    echo "  <td><a href=\"$homepage\">$name</a></td>\n";
    echo "  <td><a href=\"$url\">" . truncateUrl($url) . "</a></td>\n";
    echo "  <td>$city</td>\n";
    echo "  <td> $country</td>\n";
    echo "</tr>\n";
  }
  echo "</table>\n";
  echo "<input class=\"button\" type=\"submit\" value=\"Update / Delete\" />";
}

################# utils #################################################

function normalize_special_characters( $str )
{
    # Quotes cleanup
    $str = ereg_replace( chr(ord("`")), "'", $str );        # `
    $str = ereg_replace( chr(ord("´")), "'", $str );        # ´
    $str = ereg_replace( chr(ord("„")), ",", $str );        # „
    $str = ereg_replace( chr(ord("`")), "'", $str );        # `
    $str = ereg_replace( chr(ord("´")), "'", $str );        # ´
    $str = ereg_replace( chr(ord("“")), "\"", $str );        # “
    $str = ereg_replace( chr(ord("”")), "\"", $str );        # ”
    $str = ereg_replace( chr(ord("´")), "'", $str );        # ´

    $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                                'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                                'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                                'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                                'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
    $str = strtr( $str, $unwanted_array );

    # Bullets, dashes, and trademarks
    $str = ereg_replace( chr(149), "&#8226;", $str );    # bullet •
    $str = ereg_replace( chr(150), "&ndash;", $str );    # en dash
    $str = ereg_replace( chr(151), "&mdash;", $str );    # em dash
    $str = ereg_replace( chr(153), "&#8482;", $str );    # trademark
    $str = ereg_replace( chr(169), "&copy;", $str );    # copyright mark
    $str = ereg_replace( chr(174), "&reg;", $str );        # registration mark

    return $str;
}

?>
