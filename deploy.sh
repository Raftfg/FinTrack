#!/bin/bash
set -e

echo "==> Waiting for PostgreSQL to be ready..."
MAX_RETRIES=40
COUNT=0

# Direct PHP PDO connection test (more reliable than artisan db:show)
DB_CHECK='
$url  = getenv("DATABASE_URL") ?: getenv("DB_URL");
$host = getenv("DB_HOST") ?: "localhost";
$port = getenv("DB_PORT") ?: "5432";
$name = getenv("DB_DATABASE") ?: "railway";
$user = getenv("DB_USERNAME") ?: "postgres";
$pass = getenv("DB_PASSWORD") ?: "";

try {
    if ($url) {
        $pdo = new PDO($url, null, null, [PDO::ATTR_TIMEOUT => 2]);
    } else {
        $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$name", $user, $pass, [PDO::ATTR_TIMEOUT => 2]);
    }
    exit(0);
} catch (PDOException $e) {
    fwrite(STDERR, "DB check: " . $e->getMessage() . PHP_EOL);
    exit(1);
}
'

until php -r "$DB_CHECK" 2>/dev/null; do
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
