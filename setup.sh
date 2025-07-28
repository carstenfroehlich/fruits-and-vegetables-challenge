#!/bin/bash

echo "🚀 Fruits & Vegetables Setup with Permission Fix"
echo "=============================================="

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Get user info
USER_ID=$(id -u)
GROUP_ID=$(id -g)

echo -e "${YELLOW}Setting up for user: $USER (UID: $USER_ID, GID: $GROUP_ID)${NC}"

# Create .env.docker
cat > .env.docker << EOF
USER_ID=$USER_ID
GROUP_ID=$GROUP_ID
EOF

# Clean up
echo -e "${YELLOW}Cleaning up old installation...${NC}"
docker-compose down -v 2>/dev/null || true
sudo rm -rf vendor/ var/ composer.lock 2>/dev/null || true

# Build and start
echo -e "${YELLOW}Building containers...${NC}"
docker-compose --env-file .env.docker build --no-cache

echo -e "${YELLOW}Starting containers...${NC}"
docker-compose --env-file .env.docker up -d

# Wait for containers
sleep 5

# Install dependencies
echo -e "${YELLOW}Installing dependencies...${NC}"
docker-compose --env-file .env.docker exec php composer install --no-interaction

# Fix permissions
echo -e "${YELLOW}Setting permissions...${NC}"
docker-compose --env-file .env.docker exec php chmod +x bin/console bin/phpunit 2>/dev/null || true
docker-compose --env-file .env.docker exec php chmod -R 777 var/

# Clear cache
echo -e "${YELLOW}Clearing cache...${NC}"
docker-compose --env-file .env.docker exec php php bin/console cache:clear

# Load data
echo -e "${YELLOW}Loading sample data...${NC}"
docker-compose --env-file .env.docker exec php php bin/console app:load-data

echo -e "${GREEN}✅ Setup complete!${NC}"
echo -e "${GREEN}🌐 Application: http://localhost:8080${NC}"