#!/bin/bash

wget --quiet -O /tmp/radio.html http://www.surfmusic.de/radio-station/xxx,$1.html
html2xhtml /tmp/radio.html -o /tmp/radio.xhtml
sed -i -e 's/&nbsp;/ /g' /tmp/radio.xhtml
php5 -f getradios.php
. /tmp/getradios_vars.sh
echo "Name: $name"
echo "Homepage: $homepage"
echo "City: $city"
echo "Country: $country"
echo "URL: $url"

