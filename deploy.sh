#!/bin/bash
set -e

echo "==> Waiting for PostgreSQL to be ready..."
MAX_RETRIES=30
COUNT=0

until php artisan db:show > /dev/null 2>&1; do
    COUNT=$((COUNT + 1))
    if [ "$COUNT" -ge "$MAX_RETRIES" ]; then
        echo "ERROR: PostgreSQL did not become ready after ${MAX_RETRIES} retries. Aborting."
        exit 1
    fi
    echo "Database not ready yet (attempt ${COUNT}/${MAX_RETRIES}), retrying in 3 seconds..."
    sleep 3
done

echo "==> PostgreSQL is ready! Running deployment tasks..."

php artisan migrate --force
php artisan storage:link || true
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Deployment tasks completed successfully!"
