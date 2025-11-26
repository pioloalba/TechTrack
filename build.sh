#!/bin/bash
# Render build script for TechTrack

echo "Starting TechTrack build process..."

# Install Composer dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# Create runtime directories if they don't exist
echo "Setting up runtime directories..."
mkdir -p runtime/cache
mkdir -p runtime/logs

# Set permissions (may not persist on Render, but good practice)
chmod -R 777 runtime/cache
chmod -R 777 runtime/logs

echo "Build completed successfully!"
