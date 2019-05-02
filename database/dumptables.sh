#!/bin/bash

DBNAME='Set this before using'

mysql -B --skip-column-names -e "show tables like 'wp\_oa\_%'" "$DBNAME" | xargs mysqldump --no-data --no-create-db --routines --create-options "$DBNAME" | grep -v '^-- Host:' | grep -v "DEFINER=" | sed -e 's/InnoDB AUTO_INCREMENT=[[:digit:]]\+ /InnoDB /' > tables.sql
