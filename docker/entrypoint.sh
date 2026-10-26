#!/bin/sh
set -eu

data_dir=/app/database/data
key_file="$data_dir/app.key"

mkdir -p "$data_dir"
touch "$DB_DATABASE"

if [ ! -s "$key_file" ]; then
    (umask 077 && php artisan key:generate --show --no-ansi > "$key_file")
fi

APP_KEY="$(cat "$key_file")"
export APP_KEY

php artisan migrate --force

exec php artisan serve --host=0.0.0.0 --port=8000 --no-reload
