<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
// init db connection
include '../lib/lib.php';

opendb();

// generate country list once for all
$query = "SELECT `country` FROM `countries` ORDER by country";
$countriessql = mysql_query($query) or die ('Datenbankabfrage fehlgeschlagen');
$country_id = 0;
while(list($country) = mysql_fetch_array($countriessql)) {
  $countries[] = array($country, $country_id);
  $country_id++;
}

class Parameter {
  var $name;
  var $type;
  var $label;
  var $initvalue;
  var $newvalue;

  function setInitValues($name, $type, $label, $initvalue) {
    global $countries;
    global $location_id;
    $this->name = $name;
    $this->type = $type;
    $this->label = $label;
    $this->initvalue = $initvalue;
    $this->newvalue = '';
    if (isset($_POST[$name])) {
      $this->newvalue = $_POST[$name];
      if ($this->name == "country") {
        $this->newvalue = $countries[$_POST[$name]][0];
      }
    }
    if ($this->name == "city") {
      $location_id = -1;
      $citytag = "cities-x";
      if (isset($_POST["country"])) {
        $citytag = "cities-" . $_POST["country"];
      }
      if(isset($_POST[$citytag])) {
        $city_id = $_POST[$citytag];
        if (is_numeric($city_id)) {
          $location_id = $city_id;
          $query = "select `city` from locations where `id` = '$city_id'";
          $citiessql = mysql_query($query) or die ('Datenbankabfrage fehlgeschlagen');
          while(list($city) = mysql_fetch_array($citiessql)) {
            $this->newvalue = $city;
          }
        } else {
          if (isset($_POST['new_city'])) {
            $this->newvalue = $_POST['new_city'];
          }
        }
      } else {
        if (isset($_POST['new_city'])) {
          $this->newvalue = $_POST['new_city'];
        }
      }
    } elseif ($this->name == "lat" and $location_id > 0) {
      $query = "select X(coord) from locations where `id` = '$location_id'";
      $xcoordsql = mysql_query($query) or die ('Datenbankabfrage fehlgeschlagen');
      while(list($xcoord) = mysql_fetch_array($xcoordsql)) {
        $this->newvalue = $xcoord;
      }
    } elseif ($this->name == "lon" and $location_id > 0) {
      $query = "select Y(coord) from locations where `id` = '$location_id'";
      $ycoordsql = mysql_query($query) or die ('Datenbankabfrage fehlgeschlagen');
      while(list($ycoord) = mysql_fetch_array($ycoordsql)) {
        $this->newvalue = $ycoord;
      }
    }
    $this->input = false;
    if (isset($_POST['input'])) {
      $this->input = true;
    }
    if ($this->name == 'lon') {
      global $lon_global;
      $lon_global = $this->newvalue;
    }
    if ($this->name == 'lat') {
      global $lat_global;
      $lat_global = $this->newvalue;
    }
  }

  function showData() {
    echo "  <tr>\n";
    echo "    <td>$this->label:</td>\n";
    echo "    <td class=\"highlight\">$this->newvalue</td>\n";
    echo "  </tr>\n";
  }

  function validate() {
    $validateFunctionName = 'validate_' . $this->name;
    $return = $validateFunctionName($this->newvalue);
    return $return;
  }

  function getFinalValue() {
    return $this->newvalue;
  }

  function generateHtml() {
    global $emptyness;
    echo "  <tr class=\"form\">\n";
    // Label
    echo "    <td>$this->label</td>\n";
    // Input
    echo "    <td>";
    global $allok;
    if ($this->name == 'radio' or $this->name == 'url' or $this->name == 'homepage') {
      if ($this->input and $allok == false) {
        $content = $this->newvalue;
      } else {
        $content = $this->initvalue;
      }
      echo "<input type=\"text\" name=\"$this->name\" value=\"$content\" />";
    } elseif ($this->name == 'country'){
      $selected = 'empty';
      if ($allok == false) {
        $selected = $this->newvalue;
      }
      generateCountrySelector($selected);
    } elseif ($this->name == 'city') {
      $country_id = 'empty';
      if (isset($_POST["country"])) { 
        $country_id = $_POST["country"];
      }
      generateCitySelectors($country_id);
      $cityid = "cities-" . $country_id;
      if ($this->input and $allok == false) {
        $content = $this->newvalue;
      } else {
        $content =  $this->initvalue;
      }
      if (isset($_POST[$cityid]) and is_numeric($_POST[$cityid])) {
        $location_id = $_POST[$cityid];
      } else {
        $location_id = -1;
      }
      if (isset($_POST[$cityid]) and $_POST[$cityid] == "new" or $emptyness == "empty" and isset($_POST['input']) and $_POST['input'] == "true" and $allok == false) {
        echo "<input class=\"visible\" type=\"text\" name=\"new_city\" value=\"$content\" />\n";
      } else {
        echo "<input type=\"text\" name=\"new_city\" />\n";
      }
    } elseif ($this->name == 'lon' or $this->name == 'lat') {
      if ($this->input and $allok == false) {
        $content = $this->newvalue;
      } else {
        $content =  $this->initvalue;
      }
      $cityid = "cities-x";
      if (isset($_POST['country'])) {
        $cityid = "cities-" . $_POST['country'];
      }
      if (isset($_POST[$cityid]) and $_POST[$cityid] == "new" or $emptyness == "empty" and isset($_POST['input']) and $_POST['input'] == "true" and $allok == false) {
        echo "<input class=\"visible\" type=\"text\" name=\"$this->name\" value=\"$content\" />\n";
      } else {
        echo "<input type=\"text\" name=\"$this->name\" value=\"$content\" />\n";
      }
    }
    echo "</td>\n";
    // Error message (if applicable)
    echo "    <td class=\"error\">";
    if ($this->input == true and $this->validate() == false) {
      echo "&lt;--- Please enter something valid here";
    }
    echo "</td>\n";
    echo "  </tr>\n";
  } 
}

# create objs
$radioobj = new Parameter;
$homepageobj = new Parameter;
$urlobj = new Parameter;
$cityobj = new Parameter;
$countryobj = new Parameter;
$latobj = new Parameter;
$lonobj = new Parameter;

# init objs
$radioobj->setInitValues("radio", "text", "Radio Station", "");
$homepageobj->setInitValues("homepage", "text", "Homepage", "http://");
$urlobj->setInitValues("url", "text", "Stream URL", "http://");
$countryobj->setInitValues("country", "select", "Country", "");
$cityobj->setInitValues("city", "select", "City", "");
$latobj->setInitValues("lat", "text", "Latitude", "0.0");
$lonobj->setInitValues("lon", "text", "Longitude", "0.0");

# is all data valid?
$allok = false;
if ($radioobj->validate() and $homepageobj->validate() and $urlobj->validate() and $cityobj->validate() and
    $countryobj->validate() and $latobj->validate() and $lonobj->validate()) {
  $allok = true;
}

// insert data into mysql db
if ($allok) {
  $radio_final = $radioobj->getFinalValue();
  $homepage_final = $homepageobj->getFinalValue();
  $url_final = $urlobj->getFinalValue();
  $country_final = $countryobj->getFinalValue();
  $city_final = $cityobj->getFinalValue();
  $lat_final = $latobj->getFinalValue();
  $lon_final = $lonobj->getFinalValue();
  if ($location_id >  0) {
    $new_location_id = $location_id;
  } else {
    $insert_city = "insert into `locations` (`city`, `country`, `coord`) values ('$city_final', '$country_final', GeomFromText('POINT($lat_final $lon_final)', 4326))";
    mysql_query($insert_city) or die ('Datenbankabfrage fehlgeschlagen');
    $get_id = "select `id` from `locations` where `country` = '$country_final' and `city` = '$city_final'";
    $result = mysql_query($get_id) or die ('Datenbankabfrage fehlgeschlagen');
    while(list($id) = mysql_fetch_array($result)) {
      $new_location_id = $id;
    } 
  }
  $insert_radio = "insert into `radios` (`name`, `homepage`, `url`, `location_id`) values ('$radio_final', '$homepage_final', '$url_final', $new_location_id)";
  mysql_query($insert_radio) or die ('Datenbankabfrage fehlgeschlagen');
}

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></meta>
<script src="mootools-core.js" type="text/javascript" ></script>
<script src="countrycityselect.js" type="text/javascript" ></script>
<link rel="stylesheet" media="all" type="text/css" href="style.css" />
<title>Manage RSK Database</title>
</head>
<body>
<?php
switcher();
?>
<h4>Enter a new Radiostation</h4>
<form name="add_radio" action="." method="post">
<table>
<?php
$radioobj->generateHtml();
$homepageobj->generateHtml();
$urlobj->generateHtml();
$countryobj->generateHtml();
$cityobj->generateHtml();
$latobj->generateHtml();
$lonobj->generateHtml();
?>
</table>
<input type="hidden" name="input" value="true" />
<input class="button" type="submit" value="Submit" />
</form>
<?php
if ($allok) {
?>
<h4>added the following data to the database</h4>
<table>
<?php
  $radioobj->showData();
  $homepageobj->showData();
  $urlobj->showData();
  $cityobj->showData();
  $countryobj->showData();
  $latobj->showData();
  $lonobj->showData();
  //echo "<tr><td>$insert_city <br/>$get_id<br/>$insert_radio</td></tr>\n";
  echo "</table>\n";
}
?>
</body>
</html>
<?php
// close db connection
closedb();
?>
