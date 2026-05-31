#!/bin/bash

MYSQL_DATA="/home/runner/mysql-data"
MYSQL_RUN="/home/runner/mysql-run"
MYSQL_LOGS="/home/runner/mysql-logs"
MYSQL_SOCK="$MYSQL_RUN/mysql.sock"

mkdir -p "$MYSQL_DATA" "$MYSQL_RUN" "$MYSQL_LOGS"

# Kill any leftover mysqld
pkill -f mysqld 2>/dev/null
sleep 1
rm -f "$MYSQL_SOCK"

# --- Initialize MariaDB data dir if mysql schema missing ---
NEED_INIT=false
if [ ! -f "$MYSQL_DATA/ibdata1" ] || [ ! -d "$MYSQL_DATA/mysql" ]; then
  NEED_INIT=true
fi

if [ "$NEED_INIT" = "true" ]; then
  echo "[start.sh] Initializing MariaDB..."
  rm -rf "$MYSQL_DATA"
  mkdir -p "$MYSQL_DATA"

  # Start mysqld in skip-grant-tables mode for init
  mysqld --no-defaults --user=runner \
    --datadir="$MYSQL_DATA" \
    --socket="$MYSQL_SOCK" \
    --port=3306 --bind-address=127.0.0.1 \
    --log-error="$MYSQL_LOGS/error.log" \
    --skip-grant-tables &

  # Wait for socket
  echo -n "[start.sh] Waiting for MariaDB socket..."
  for i in $(seq 1 30); do
    if [ -S "$MYSQL_SOCK" ]; then echo " ready!"; break; fi
    sleep 1; echo -n "."
  done

  if [ ! -S "$MYSQL_SOCK" ]; then
    echo ""
    echo "[start.sh] ERROR: MariaDB did not start for init"
    cat "$MYSQL_LOGS/error.log"
    exit 1
  fi

  # Run PHP-based initialization (avoids /proc restrictions)
  echo "[start.sh] Running database initialization..."
  php /home/runner/workspace/db_init.php
  INIT_RESULT=$?

  if [ $INIT_RESULT -ne 0 ]; then
    echo "[start.sh] ERROR: DB init failed (exit $INIT_RESULT)"
    cat "$MYSQL_LOGS/error.log"
    exit 1
  fi

  # Create app schema (tables, seed data)
  echo "[start.sh] Creating application schema..."
  php /home/runner/workspace/db_schema.php

  echo "[start.sh] Shutting down init mysqld..."
  pkill -f mysqld
  sleep 2
  rm -f "$MYSQL_SOCK"
  echo "[start.sh] Init complete."
fi

# --- Start mysqld normally ---
echo "[start.sh] Starting MariaDB (normal mode)..."
mysqld --no-defaults --user=runner \
  --datadir="$MYSQL_DATA" \
  --socket="$MYSQL_SOCK" \
  --port=3306 --bind-address=127.0.0.1 \
  --log-error="$MYSQL_LOGS/error.log" \
  --skip-name-resolve &

# Wait for socket
echo -n "[start.sh] Waiting for MariaDB..."
for i in $(seq 1 30); do
  if [ -S "$MYSQL_SOCK" ]; then echo " ready!"; break; fi
  sleep 1; echo -n "."
done
echo ""

if [ ! -S "$MYSQL_SOCK" ]; then
  echo "[start.sh] ERROR: MariaDB failed to start"
  cat "$MYSQL_LOGS/error.log"
  exit 1
fi

echo "[start.sh] MariaDB running on 127.0.0.1:3306"

# --- Test DB connection ---
php -r "
  \$c = new mysqli('127.0.0.1', 'uub4rmw23inpzxn9_pae_root', '959@M+U1GOat', 'uub4rmw23inpzxn9_erp', 3306);
  if (\$c->connect_error) { echo '[start.sh] DB connect test FAILED: ' . \$c->connect_error . PHP_EOL; }
  else { echo '[start.sh] DB connect test OK' . PHP_EOL; \$c->close(); }
" 2>&1

# --- Start PHP built-in server on port 5000 ---
echo "[start.sh] Starting PHP server on 0.0.0.0:5000..."
cd /home/runner/workspace
exec php -S 0.0.0.0:5000 router.php
