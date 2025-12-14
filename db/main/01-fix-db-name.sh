#!/bin/bash
# Fix database name references in SQL file
sed -i 's/`pae_erp`\./`uub4rmw23inpzxn9_erp`./g' /docker-entrypoint-initdb.d/pae_erp.sql
sed -i 's/USE `pae_erp`/USE `uub4rmw23inpzxn9_erp`/g' /docker-entrypoint-initdb.d/pae_erp.sql
sed -i 's/USE pae_erp/USE uub4rmw23inpzxn9_erp/g' /docker-entrypoint-initdb.d/pae_erp.sql

