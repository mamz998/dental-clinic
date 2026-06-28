#!/usr/bin/env bash
set -e

export DB_DATABASE=/var/data/database.sqlite

php artisan serve --host=0.0.0.0 --port=$PORT
