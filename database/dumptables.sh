#!/bin/bash

DBNAME='Set this before using'

mysql -B --skip-column-names -e "show tables like 'wp_oa%'" | xargs mysqldump --no-data --no-create-db --routines --create-options "$DBNAME" > tables.sql
