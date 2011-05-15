<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
// init db connection
include '../lib/lib.php';
opendb();

// INIT zeugs
if (isset($_POST['radio'])    == false) $_POST['radio']     = ''; 
if (isset($_POST['homepage']) == false) $_POST['homepage']  = 'http://'; 
if (isset($_POST['url'])      == false) $_POST['url']       = 'http://'; 
if (isset($_POST['country'])  == false) $_POST['country']   = ''; 
if (isset($_POST['city'])     == false) $_POST['city']      = '';
if (isset($_POST['complete']) == false) $_POST['complete']  = 'false'; 

function warn($type) {
  echo "bla gaggi $type";
}

function generateCountrySelectorNew() {
  $query = "SELECT `iso`, `name` FROM `countries` ORDER by `name`";
  $countriessql = mysql_query($query) or die ('Datenbankabfrage fehlgeschlagen');
  echo "<select name=\"country\" onChange=\"this.form.submit()\" id=\"country\">\n";
  echo "  <option value=\"\">Select Country...</option>\n";
  while(list($iso, $country) = mysql_fetch_array($countriessql)) {
    if ( $_POST['country'] == $iso ) {
      echo "  <option selected value=\"$iso\">$country</option>\n";
    } else {
      echo "  <option value=\"$iso\">$country</option>\n";
    }
  }
  echo "</select>\n";
}

function generateCitySelectorNew() {
  global $latitude;
  global $longitude;
  $latitude = "";
  $longitude = "";
  if ( $_POST['country'] != "" ) {
    $cc = $_POST['country'];
    $query = "SELECT `id`, `name`, `admin1_code`, X(coord), Y(coord) FROM `cities` where `country_code` = '$cc' ORDER by `name`";
    $citiessql = mysql_query($query) or die ('Datenbankabfrage fehlgeschlagen');
    echo "<select name=\"city\" onChange=\"this.form.submit()\" id=\"city\">\n";
    echo "  <option value=\"\">Select a City...</option>\n";
    while(list($id, $city, $division, $lat, $long) = mysql_fetch_array($citiessql)) {
      if ( $_POST['city'] == $id ) {
        echo "  <option selected value=\"$id\">$city ($division)</option>\n";
        $latitude = $lat;
        $longitude = $long;
      } else {
        echo "  <option value=\"$id\">$city ($division)</option>\n";
      }  
    }
    echo "</select>\n";
  }
}

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></meta>
<link rel="stylesheet" media="all" type="text/css" href="style.css" />
<title>Manage RSK Database</title>
</head>
<body>
<?php switcher(); ?>
<h4>Enter a new Radiostation</h4>
<form name="add_radio" action="." method="post">
<table>
  <tr class="form">
    <td>Radio Station</td>
    <td><input type="text" name="radio" value="<?php echo $_POST['radio']; ?>"/></td>
    <td class="error"><?php warn('radio'); ?></td>
  </tr>
  <tr class="form">
    <td>Homepage</td>
    <td><input type="text" name="homepage" value="<?php echo $_POST['homepage']; ?>" /></td>
    <td class="error"><?php warn('homepage'); ?></td>
  </tr>
  <tr class="form">
    <td>Stream URL</td>
    <td><input type="text" name="url" value="<?php echo $_POST['url']; ?>" /></td>
    <td class="error"><?php warn('url'); ?></td>
  </tr>
  <tr class="form">
    <td>Country</td>
    <td><?php generateCountrySelectorNew(); ?></td>
    <td class="error"><?php warn('country'); ?></td>
  </tr>
  <tr class="form">
    <td>City</td>
    <td><?php generateCitySelectorNew(); ?></td>
    <td class="error"><?php warn('city'); ?></td>
  </tr>
  <tr class="form">
    <td>Latitude</td>
    <td><?php echo $latitude; ?></td>
    <td class="error"><?php warn('lat'); ?></td>
  </tr>
  <tr class="form">
    <td>Longitude</td>
    <td><?php echo $longitude; ?></td>
    <td class="error"><?php warn('lon'); ?></td>
  </tr>
</table>
<input type="hidden" name="complete" value="false"/>
<?php echo $_POST['complete']; ?>
<input class="button" type="submit" value="Submit" onclick="this.form.complete.value='true'"/>
</form>
</body>
</html>
<?php closedb(); ?>
