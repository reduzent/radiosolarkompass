#!/bin/bash
. mysqlaccess
start_id=$1
mysql="mysql -u ${user} -p${pass} -Daudiosolar --protocol=TCP  --skip-column-names"
echo "Retrieving checklist...."
ids_to_check=$(echo "select id from radios_import where country_valid = true and url_valid = false;" | $mysql)

for id in $ids_to_check
do
  if [ $id -ge $start_id ]
  then
    url=$(echo "select url from radios_import where id = $id;" | $mysql) 
    #echo "Checking streamurl: $url ..."
    $(pd -nomidi -noaudio -nrt -nogui -open getradios_checkstream.pd -send "streamurl $url" 2>&1 | grep readyforradiosolarkompass)
    if [ $readyforradiosolarkompass = "true" ]
    then
      echo -e "VALID:\t$id\t$url"
      echo "update radios_import set url_valid = true where id = $id" | $mysql
    else
      echo -e "BROKEN:\t$id\t$url"
    fi
  fi
done
