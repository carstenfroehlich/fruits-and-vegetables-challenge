# 🍎🥕 Fruits and Vegetables Service

A RESTful API service built with Symfony 7.3 and PHP 8.3 that manages collections of fruits and vegetables. The service processes JSON data, stores items in separate collections based on their type, and provides various endpoints for data manipulation.

## 📋 Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Project Setup](#project-setup)
- [Running the Application](#running-the-application)
- [Running Unit Tests](#running-unit-tests)
- [API Documentation](#api-documentation)
- [Postman Collection](#postman-collection)
- [Architecture](#architecture)

## ✨ Features

- **Type-based Collections**: Automatic separation of fruits and vegetables
- **Weight Management**: All weights stored internally in grams with automatic unit conversion
- **RESTful API**: Full CRUD operations for managing items
- **Search & Filter**: Query collections with various filters
- **File-based Persistence**: Data persisted between requests using file storage
- **Clean Architecture**: Domain-Driven Design with clear separation of concerns
- **Modern PHP**: Uses PHP 8.3 features like readonly properties and attributes
- **Comprehensive Tests**: Unit tests for all core functionality

## 🔧 Requirements

- Docker and Docker Compose
- Git
- (Optional) Make for easier command execution

## 🚀 Project Setup

### 1. Clone the Repository

```bash
git clone https://github.com/carstenfroehlich/fruits-and-vegetables-challenge.git
cd fruits-and-vegetables-challenge
```

### 2. Initial Setup with Docker

```bash
# Start Docker containers
docker-compose up -d --build

# Install PHP dependencies
docker-compose exec --user root php composer install --no-interaction

# Set permissions
docker-compose exec --user root php chmod +x bin/console
docker-compose exec --user root php chmod -R 777 var/

# Clear cache
docker-compose exec php php bin/console cache:clear
```

### Alternative: One-Command Setup

```bash
# If you have Make installed
make setup

# Or run this script
./setup.sh
```

## 🏃 Running the Application

### Start the Application

```bash
# Start containers
docker-compose up -d

# Check if running
docker-compose ps
```

The application will be available at: **http://localhost:8080**

### Stop the Application

```bash
# Stop containers
docker-compose down

# Stop and remove volumes
docker-compose down -v
```

### View Logs

```bash
# View all logs
docker-compose logs -f

# View specific service logs
docker-compose logs -f php
docker-compose logs -f nginx
```

## 🧪 Running Unit Tests

### Execute All Tests

```bash
# Run all tests
docker-compose exec php php vendor/phpunit/phpunit/phpunit tests/

# With detailed output
docker-compose exec php php vendor/phpunit/phpunit/phpunit tests/ --testdox
```

### Execute Specific Tests

```bash
# Run only unit tests
docker-compose exec php php vendor/phpunit/phpunit/phpunit tests/Unit

# Run specific test file
docker-compose exec php php vendor/phpunit/phpunit/phpunit tests/Unit/Domain/Model/ItemTest.php

# Run with coverage (if XDebug is installed)
docker-compose exec php php vendor/phpunit/phpunit/phpunit tests/ --coverage-text
```

### Test Results

The project includes tests for:
- Domain Models (Fruit, Vegetable, Weight)
- Value Objects
- Services (ItemProcessorService)
- Collections

Expected output:
```
PHPUnit 11.5.27 by Sebastian Bergmann and contributors.

.......                                                             7 / 7 (100%)

Time: 00:00.050, Memory: 4.00 MB

OK (7 tests, 19 assertions)
```

## 📚 API Documentation

### Base URL
```
http://localhost:8080/api
```

### Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/load-data` | Load initial data from JSON |
| GET | `/api/collections` | Get all collections |
| GET | `/api/collections/{type}` | Get specific collection (fruit/vegetable) |
| POST | `/api/items` | Add new item |
| DELETE | `/api/items/{type}/{id}` | Delete specific item |

### Query Parameters for GET Collections

- `search` - Search items by name
- `unit` - Return weights in 'g' or 'kg' (default: 'g')
- `minWeight` - Filter by minimum weight (in grams)
- `maxWeight` - Filter by maximum weight (in grams)

## 📮 Postman Collection

The project includes a Postman collection (`roadsurfer.postman_collection.json`) with pre-configured requests for all endpoints.

### Collection Contents

#### 1. **Load Data** (`POST /api/load-data`)
Loads the initial dataset of 20 items (fruits and vegetables) into the system. The request body contains a JSON array with items having the following structure:
```json
{
  "id": 1,
  "name": "Carrot",
  "type": "vegetable",
  "quantity": 10922,
  "unit": "g"
}
```

#### 2. **Add Item** (`POST /api/items`)
Adds a new item to the collection. Example adds cauliflower as a vegetable:
```json
{
  "id": 21,
  "name": "cauliflower",
  "type": "vegetable",
  "quantity": 300,
  "unit": "kg"
}
```

#### 3. **Get All Collections** (`GET /api/collections`)
Retrieves both fruit and vegetable collections with their counts and items.

#### 4. **Get Fruits** (`GET /api/collections/fruit`)
Retrieves only the fruit collection with all fruit items.

#### 5. **Get Vegetables** (`GET /api/collections/vegetable`)
Retrieves only the vegetable collection with all vegetable items.

#### 6. **Delete Item** (`DELETE /api/items/vegetable/21`)
Removes a specific item from a collection. The example deletes the cauliflower (ID: 21) from vegetables.

### Using the Postman Collection

1. Import the collection into Postman
2. Execute requests in this order:
   - First: "Load Data" to populate the collections
   - Then: Try other operations like get, add, or delete

### Example Workflow

```bash
# 1. Load initial data
POST http://localhost:8080/api/load-data

# 2. View all collections
GET http://localhost:8080/api/collections

# 3. Add a new vegetable
POST http://localhost:8080/api/items
{
    "id": 21,
    "name": "cauliflower",
    "type": "vegetable",
    "quantity": 300,
    "unit": "kg"
}

# 4. Verify it was added
GET http://localhost:8080/api/collections/vegetable

# 5. Delete the item
DELETE http://localhost:8080/api/items/vegetable/21

# 6. Verify it was removed
GET http://localhost:8080/api/collections/vegetable
```

## 🏗️ Architecture

### Domain-Driven Design

The project follows DDD principles with clear separation:

- **Domain Layer**: Core business logic (Item, Fruit, Vegetable, Weight)
- **Application Layer**: Services and DTOs
- **Infrastructure Layer**: Storage implementation
- **Presentation Layer**: REST API Controllers

### Key Components

- **Models**: `Item` (abstract), `Fruit`, `Vegetable`
- **Value Objects**: `Weight` (handles unit conversion)
- **Collections**: Type-safe collections for fruits and vegetables
- **Services**: `ItemProcessorService`, `CollectionService`
- **Storage**: File-based persistence using `FileStorage`

### Storage

The application uses file-based storage to persist data between requests. Collections are serialized and stored in the file system, ensuring data persistence across PHP-FPM worker processes.

## 🐛 Troubleshooting

### Container Issues
```bash
# Check container status
docker-compose ps

# Rebuild containers
docker-compose down
docker-compose up -d --build
```

### Permission Issues
```bash
# Fix permissions
docker-compose exec --user root php chown -R www-data:www-data /var/www/html
docker-compose exec --user root php chmod -R 777 var/
```

### Cache Issues
```bash
# Clear all caches
docker-compose exec php rm -rf var/cache/*
docker-compose exec php php bin/console cache:clear
```

## 📝 Notes

- Data is stored in memory/files and will be lost when containers are recreated
- The initial data file `request.json` contains 20 items
- All weights are stored internally in grams
- The API supports both 'g' and 'kg' units for input and output

## 📄 License

This project was created as a technical assessment and is not licensed for public use.