#!/bin/bash
set -e

# Fix permissions at runtime
if [ -d "/var/www/html" ]; then
    # Ensure var directories exist
    mkdir -p /var/www/html/var/cache /var/www/html/var/log

    # Fix composer cache permissions
    mkdir -p /var/www/.composer
    chmod -R 775 /var/www/.composer

    # Configure git for current directory
    git config --global --add safe.directory /var/www/html
fi

# Run the original command
exec "$@"