#!/bin/bash
pos=$1
. pdaccess
echo "select id, url from radios limit $pos, 1;" | \
  mysql -u $user -p${pass} --skip-column-names radiosolarkompass
