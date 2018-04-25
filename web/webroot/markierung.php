<?php
include_once '../lib/lib.php';
opendb();

// GET CURRENT COORDINATES FOR THE LOCATION MARK
$query = "
     select
       X(`cities`.`coord`),
       Y(`cities`.`coord`)
    from `status` 
    left join `radios` on 
      `radios`.`id` = `status`.`value` 
    join `cities` on 
      `cities`.`id` = `radios`.`city_id`
    where `status`.`param` = 'onair'
    ";

$result = mysqli_query($conn, $query) or die ('Datenbank-Abfrage fehlgeschlagen');
list($lat, $lon) = mysqli_fetch_array($result);

// CONVERT WGS1984 COORDINATES TO WORLDMAP IMAGE POSITIONS IN PERCENT WITH 0%/0% BEING THE UPPER LEFT CORNER
$x_perc = 0.9671875 * ($lon / 4.0684 + 48.36);
$y_perc = rad2deg(1.25 * log(tan(0.4 * deg2rad($lat) + pi() / 4 ))) / -2.36611 + 50;

closedb();
?>
     <div id="location" style="left:<?php echo $x_perc; ?>%;top:<?php echo $y_perc; ?>%;">
       <img  id="aussenden" src="pix/aussenden3_6.png"  alt="o" />
     </div>
