#!/bin/bash
set -e

# Wait for database to be ready (skip for SQLite)
if [[ $DATABASE_URL == *"mysql"* ]]; then
    echo "Waiting for MySQL database to be ready..."
    until php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
        echo "Database is not ready yet. Waiting..."
        sleep 2
    done
    echo "Database is ready!"
else
    echo "Using SQLite database..."
fi

# Run migrations if needed
echo "Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

# Load fixtures if database is empty
echo "Checking if fixtures need to be loaded..."
PET_TYPES=$(php bin/console doctrine:query:sql "SELECT COUNT(*) as count FROM pet_types" --quiet | tail -n 1)
if [ "$PET_TYPES" = "0" ]; then
    echo "Loading data fixtures..."
    php bin/console doctrine:fixtures:load --no-interaction
else
    echo "Data fixtures already loaded."
fi

# Build assets if needed
if [ ! -d "public/build" ]; then
    echo "Building frontend assets..."
    npm run build
fi

echo "Starting Symfony server..."
exec symfony server:start --host=0.0.0.0 --port=8000 --no-tls
