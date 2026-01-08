#!/bin/bash
set -e

echo "=========================================="
echo "Initializing PAE ERP Databases"
echo "=========================================="

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
until mysqladmin ping -h localhost -u root -proot_password_2024 --silent; do
    echo "MySQL is unavailable - sleeping"
    sleep 2
done

echo "MySQL is ready!"

# Create databases
echo "Creating databases..."
mysql -h localhost -u root -proot_password_2024 <<EOF
CREATE DATABASE IF NOT EXISTS \`uub4rmw23inpzxn9_erp\` CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE DATABASE IF NOT EXISTS \`uub4rmw23inpzxn9_erp_audit\` CHARACTER SET utf8 COLLATE utf8_general_ci;
EOF

echo "Databases created successfully!"

# Create user and grant privileges
echo "Creating user and granting privileges..."
mysql -h localhost -u root -proot_password_2024 <<EOF
CREATE USER IF NOT EXISTS 'uub4rmw23inpzxn9_pae_root'@'%' IDENTIFIED BY '959@M+U1GOat';
GRANT ALL PRIVILEGES ON \`uub4rmw23inpzxn9_erp\`.* TO 'uub4rmw23inpzxn9_pae_root'@'%';
GRANT ALL PRIVILEGES ON \`uub4rmw23inpzxn9_erp_audit\`.* TO 'uub4rmw23inpzxn9_pae_root'@'%';
FLUSH PRIVILEGES;
EOF

echo "User created and privileges granted!"

# Import SQL files
echo "Importing SQL files..."

if [ -f /docker-entrypoint-initdb.d/sql/erp.sql ]; then
    echo "Importing erp.sql (this may take several minutes)..."
    mysql -h localhost -u root -proot_password_2024 uub4rmw23inpzxn9_erp < /docker-entrypoint-initdb.d/sql/erp.sql
    echo "erp.sql imported successfully!"
else
    echo "Warning: erp.sql not found, skipping..."
fi

if [ -f /docker-entrypoint-initdb.d/sql/erp_audit.sql ]; then
    echo "Importing erp_audit.sql (this may take several minutes)..."
    mysql -h localhost -u root -proot_password_2024 uub4rmw23inpzxn9_erp_audit < /docker-entrypoint-initdb.d/sql/erp_audit.sql
    echo "erp_audit.sql imported successfully!"
else
    echo "Warning: erp_audit.sql not found, skipping..."
fi

echo "=========================================="
echo "Database initialization completed!"
echo "=========================================="
echo "Main Database: uub4rmw23inpzxn9_erp"
echo "Audit Database: uub4rmw23inpzxn9_erp_audit"
echo "User: uub4rmw23inpzxn9_pae_root"
echo "=========================================="


