#!/bin/bash

function extract_stream_data {
  smid=$1
  readyforradiosolarkompass="false"
  wget --quiet -O /tmp/radio.html http://www.surfmusic.de/radio-station/xxx,${smid}.html
  if [[ $(wc -l /tmp/radio.html | cut -f1 -d" ") > 1 ]]
  then
    html2xhtml /tmp/radio.html -o /tmp/radio.xhtml
    sed -i -e 's/&nbsp;/ /g' /tmp/radio.xhtml
    php5 -f getradios.php
    . /tmp/getradios_vars.sh
    #echo "Name: $name"
    #echo "Homepage: $homepage"
    #echo "City: $city"
    #echo "Country: $country"
    #echo "URL: $url"
    $(pd -nomidi -noaudio -nrt -nogui -open getradios_checkstream.pd -send "streamurl http://www.netpd.org:8010/netpd.mp3" 2>&1 | grep readyforradiosolarkompass)
    if [ $readyforradiosolarkompass = "true" ]
    then
      echo -ne "${name};${homepage};${city};${country};${url}\n"
    fi
  fi
}

startsmid=${1:-1}

for smid in $(seq $startsmid 20000)
do
  extract_stream_data $smid
done
 
