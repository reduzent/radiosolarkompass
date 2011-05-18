<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
// init db connection
include '../lib/lib.php';
opendb();

// INIT zeugs
// default values
$radio    = '';
$homepage = 'http://';
$url      = 'http://';
$country  = 'empty';
$city     = 'empty';
$complete = false;

// post data
if (isset($_POST['radio'])   ) $radio    = $_POST['radio']   ; 
if (isset($_POST['homepage'])) $homepage = $_POST['homepage'];
if (isset($_POST['url'])     ) $url      = $_POST['url']     ;
if (isset($_POST['country']) ) $country  = $_POST['country'] ;
if (isset($_POST['city'])    ) $city     = $_POST['city']    ;
if (isset($_POST['complete']) and $_POST['complete']  == "true") $complete = true;

// form complete and data valid? 
$radio_valid    = validate_radio($radio);
$homepage_valid = validate_homepage($homepage);
$url_valid      = validate_url($url);
$country_valid  = validate_country($country);
$city_valid     = validate_city($city);
$allok          = false;
if ( $radio_valid
 and $homepage_valid
 and $url_valid
 and $country_valid
 and $city_valid
 and $complete ) {
  $allok = true;
  $todb['radio']    = $radio;
  $todb['homepage'] = $homepage;
  $todb['url']      = $url;
  $todb['country']  = $country;
  $todb['city']     = $city;
  $radio    = '';
  $homepage = 'http://';
  $url      = 'http://';
  $country  = 'empty';
  $city     = 'empty';
  $complete = false; 
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
    <td><input type="text" name="radio" value="<?php echo $radio; ?>"/></td>
    <td class="error"><?php warn('radio'); ?></td>
  </tr>
  <tr class="form">
    <td>Homepage</td>
    <td><input type="text" name="homepage" value="<?php echo $homepage; ?>" /></td>
    <td class="error"><?php warn('homepage'); ?></td>
  </tr>
  <tr class="form">
    <td>Stream URL</td>
    <td><input type="text" name="url" value="<?php echo $url; ?>" /></td>
    <td class="error"><?php warn('url'); ?></td>
  </tr>
  <tr class="form">
    <td>Country</td>
    <td><?php generateCountrySelector(); ?></td>
    <td class="error"><?php warn('country'); ?></td>
  </tr>
  <tr class="form">
    <td>City</td>
    <td><?php generateCitySelector(); ?></td>
    <td class="error"><?php warn('city'); ?></td>
  </tr>
  <tr class="form">
    <td>Latitude</td>
    <td><?php echo $latitude; ?></td>
    <td class="error"></td>
  </tr>
  <tr class="form">
    <td>Longitude</td>
    <td><?php echo $longitude; ?></td>
    <td class="error"></td>
  </tr>
</table>
<input type="hidden" name="complete" value="false"/>
<input class="button" type="submit" value="Submit" onclick="this.form.complete.value='true'"/>
<br />
<?php 
addShowNewEntry();
?>
</form>
</body>
</html>
<?php closedb(); ?>
