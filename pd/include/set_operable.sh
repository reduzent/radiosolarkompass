#!/bin/bash
id=$1
operable=$2
. pdaccess
echo "update radios set operable = $operable, last_checked = current_date() where id = $id;" | \
  mysql -u $user -p${pass} --skip-column-names radiosolarkompass
