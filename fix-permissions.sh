#!/bin/bash

echo "🔧 Fixing Docker permissions..."

# Get current user and group ID
USER_ID=$(id -u)
GROUP_ID=$(id -g)

echo "Current user ID: $USER_ID"
echo "Current group ID: $GROUP_ID"

# Create .env.docker file
cat > .env.docker << EOF
USER_ID=$USER_ID
GROUP_ID=$GROUP_ID
EOF

echo "✅ Created .env.docker with your user settings"

# Stop containers
echo "Stopping containers..."
docker-compose down

# Remove old images
echo "Removing old images..."
docker rmi fruits_vegetables_php 2>/dev/null || true

# Rebuild with correct permissions
echo "Rebuilding containers with correct permissions..."
docker-compose --env-file .env.docker up -d --build

# Wait for containers
sleep 5

# Fix any remaining permission issues
echo "Fixing file permissions..."
sudo chown -R $USER_ID:$GROUP_ID .
chmod -R 755 .
chmod -R 777 var/ 2>/dev/null || true

echo "✅ Permissions fixed!"