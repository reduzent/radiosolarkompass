#!/bin/bash

function extract_stream_data {
  smid=$1
  echo "Counter: $smid"
  readyforradiosolarkompass="false"
  ugent="Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.2.17) Gecko/20110422 Ubuntu/10.04 (lucid) Firefox/3.6.17"
  wget --quiet -O /tmp/radio.html --user-agent="$uagent" http://www.surfmusic.de/radio-station/xxx,${smid}.html
  if [ $(wc -l /tmp/radio.html | cut -f1 -d" ") -gt 1 ]
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
    if [ "$url" != "http://www.surfmusik.org/enostream.mp3" ]
    then 
      echo "Checking streamurl: $url ..."
      $(pd -nomidi -noaudio -nrt -nogui -open getradios_checkstream.pd -send "streamurl $url" 2>&1 | grep readyforradiosolarkompass)
      if [ $readyforradiosolarkompass = "true" ]
      then
        echo -ne "${smid};${name};${homepage};${city};${country};${url}\n" >> /home/roman/surfmusic_streamdata.csv
        echo "STREAM OK ($name, $city, $country)"
      fi
    fi
  fi
}

startsmid=${1:-1}

for smid in $(seq $startsmid 20000)
do
  extract_stream_data $smid
done
 
