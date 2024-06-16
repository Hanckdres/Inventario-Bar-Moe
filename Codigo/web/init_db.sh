#!/bin/bash

# Esperar a que el servicio MySQL esté disponible
until mysql -hmysql -uroot -pmy-secret-pw -e ";" 2>/dev/null; do
    echo "Waiting for MySQL to be ready..."
    sleep 1
done

# Ejecutar el script SQL de inicialización
mysql -hmysql -uroot -pmy-secret-pw < /docker-entrypoint-initdb.d/init.sql
