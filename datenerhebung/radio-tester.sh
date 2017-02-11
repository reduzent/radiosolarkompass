#!/bin/bash
getstatement="
begin;

lock tables radios write;

select @radio := id, url
from radios 
where operable <> -10
order by last_checked asc
limit 1;

update radios
set operable = -10 
where id = @radio;

unlock tables;

commit;
"

id_url=$(mysql -s -N -e "$getstatement")
id=$(echo "$id_url" | cut -f1)
url=$(echo "$id_url" | cut -f2)

echo -n "$id_url ... "
pdout=$(pd -stderr \
   -nogui \
   -send "URL $url" \
   -open radio-tester.pd 2>&1)

pdexit=$?

if [ "$pdexit" == 139 ]
then
  operable=-1
elif [ "$pdexit" == 0 ]
then
  operable=$(echo "$pdout" | grep '^OPERABLE' | sed 's/^OPERABLE //')
else
  echo "FEHLER!!"
  exit 1
fi

echo "$operable"

updatestatement="
update radios
set operable = $operable, last_checked = now()
where id = $id;
"
mysql -s -N -e "$updatestatement"
exit 0

