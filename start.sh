#!/bin/bash
# Railway startup script

# Create runtime directories
mkdir -p runtime/cache runtime/logs public/uploads
chmod -R 777 runtime public/uploads

# Start PHP server
php -S 0.0.0.0:$PORT -t .
