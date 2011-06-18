#!/bin/bash
. mysqlaccess
mysql="mysql -u ${user} -p${pass} -Dradiosolarkompass --protocol=TCP  --skip-column-names"

idstocheck=$(echo "select id from radios_import where country_valid = true and url_valid = true and city_id = 0 and id > 13000;" | $mysql)

for id in $idstocheck
do
  cityidraw=$(echo "select geonames_cities.id from radios_import, geonames_countries, geonames_cities where radios_import.country = geonames_countries.name and radios_import.city = geonames_cities.name and geonames_cities.country_code = geonames_countries.iso and radios_import.id = $id;" | $mysql)
  #echo "id: $id city: $cityidraw"
  cityidnum=$(echo $cityidraw | wc -w)
  if [ $cityidnum -eq 1 ]
  then
    echo "update radios_import set city_id = ${cityidraw} where id = ${id};" | $mysql
    echo "done: $id"
  fi 
done
