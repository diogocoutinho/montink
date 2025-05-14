#!/bin/bash

# Stop and remove existing containers
docker-compose down

# Remove existing database data
rm -rf docker/mysql/db_data/*

# Start containers
docker-compose up -d

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
sleep 10

# Show container status
docker-compose ps

echo "Environment is ready! Access the application at http://localhost:8080" 