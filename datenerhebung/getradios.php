<?php
$file = fopen('/tmp/getradios_vars.sh', 'w');
$xmlobj = simplexml_load_file('/tmp/radio.xhtml');
$radioelement = $xmlobj->body->div->table->tr->td->div->center->table->tr->td[1]->div->table->tr->td[1]->a;
$cityelement = $xmlobj->body->div->table->tr->td->div->center->table->tr->td[1]->div->table->tr[1]->td[1];
$urlelement = $xmlobj->body->div->table->tr->td->div->center->table->tr->td[1]->div->table->tr[6]->td[1]->p->a;
$url = trim((string)$urlelement->attributes()->{'href'});
$citycountry = trim((string)$cityelement);
list($city, $country) = explode(' - ', $citycountry);
$name = trim((string)$radioelement);
$name = ereg_replace("[ \t\n\r]+", " ", $name);
$homepage = trim((string)$radioelement->attributes()->{'href'});
$bashvars = "name=\"$name\";\nhomepage=\"$homepage\";\ncity=\"$city\";\ncountry=\"$country\";\nurl=\"$url\";\n";
fwrite($file, $bashvars);
fclose($file);
?>

