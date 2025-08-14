# 🐾 HLL DocuPet - macOS Setup Guide

Complete step-by-step instructions to run the DocuPet application on macOS using Docker.

## 📋 Prerequisites

### 1. Install Docker Desktop for Mac

1. **Download Docker Desktop:**
   - Visit: https://www.docker.com/products/docker-desktop/
   - Click "Download for Mac"
   - Choose the appropriate version for your Mac (Intel or Apple Silicon)

2. **Install Docker Desktop:**
   ```bash
   # Open the downloaded .dmg file and drag Docker to Applications
   # Launch Docker Desktop from Applications
   # Follow the setup wizard
   ```

3. **Verify Installation:**
   ```bash
   docker --version
   docker-compose --version
   ```

### 2. Install Git (if not already installed)

```bash
# Check if git is installed
git --version

# If not installed, install via Homebrew
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
brew install git
```

## 🚀 Quick Start

### 1. Clone the Repository

```bash
# Clone the project
git clone <repository-url>
cd Jonas_docupet

# Or if you have the project files already, navigate to the directory
cd path/to/Jonas_docupet
```

### 2. Start the Application

```bash
# Build and start all services
docker-compose up --build

# Or run in background
docker-compose up --build -d
```

### 3. Setup Database

```bash
# Wait for containers to start, then run database setup
docker-compose exec app php bin/console doctrine:migrations:migrate --no-interaction
docker-compose exec app php bin/console doctrine:fixtures:load --no-interaction
```

### 4. Access the Application

- **Main Application:** http://localhost:8001
- **Pet Registration:** http://localhost:8001/pet/register
- **Pet List:** http://localhost:8001/pet/list

## 🔧 Detailed Setup Steps

### Step 1: Verify Docker is Running

```bash
# Check Docker status
docker info

# If Docker is not running, start Docker Desktop from Applications
```

### Step 2: Build the Application

```bash
# Navigate to project directory
cd Jonas_docupet

# Build the Docker images
docker-compose build

# This will:
# - Build PHP 8.3 with Apache
# - Install all PHP dependencies
# - Set up MySQL 8.0 database
```

### Step 3: Start Services

```bash
# Start all services
docker-compose up

# You should see output like:
# ✅ Database container starting
# ✅ Application container starting
# ✅ Apache server running on port 80
```

### Step 4: Initialize Database

```bash
# In a new terminal window, run database migrations
docker-compose exec app php bin/console doctrine:migrations:migrate --no-interaction

# Load sample data
docker-compose exec app php bin/console doctrine:fixtures:load --no-interaction

# Verify database setup
docker-compose exec app php bin/console doctrine:schema:validate
```

## 🧪 Testing

### Run All Tests

```bash
# Run PHPUnit tests
docker-compose exec app php bin/phpunit

# Run specific test file
docker-compose exec app php bin/phpunit tests/Controller/PetControllerTest.php

# Run manual test script
docker-compose exec app php test_registration.php
```

### Test the Application

1. **Visit:** http://localhost:8001
2. **Register a Pet:**
   - Go to http://localhost:8001/pet/register
   - Fill out Step 1: Pet name and type
   - Fill out Step 2: Breed information
   - Fill out Step 3: Gender and age
   - Complete registration

3. **View Registered Pets:**
   - Go to http://localhost:8001/pet/list
   - See all registered pets

## 🛠 Development Commands

### Container Management

```bash
# Start services in background
docker-compose up -d

# Stop services
docker-compose down

# Restart services
docker-compose restart

# View logs
docker-compose logs app
docker-compose logs db

# Access application container shell
docker-compose exec app bash

# Access database
docker-compose exec db mysql -u root -p hll_docupet
# Password: password
```

### Application Commands

```bash
# Clear cache
docker-compose exec app php bin/console cache:clear

# Update database schema
docker-compose exec app php bin/console doctrine:schema:update --force

# Create new migration
docker-compose exec app php bin/console make:migration

# Run specific migration
docker-compose exec app php bin/console doctrine:migrations:execute --up VERSION
```

## 🔍 Troubleshooting

### Common Issues

1. **Port Already in Use:**
   ```bash
   # If port 8001 is busy, change in docker-compose.yml:
   # ports: - "8002:80"  # Use port 8002 instead
   ```

2. **Database Connection Issues:**
   ```bash
   # Restart database container
   docker-compose restart db
   
   # Check database logs
   docker-compose logs db
   ```

3. **Permission Issues:**
   ```bash
   # Fix file permissions
   docker-compose exec app chown -R www-data:www-data /var/www/html
   docker-compose exec app chmod -R 755 /var/www/html
   ```

4. **Clear Everything and Start Fresh:**
   ```bash
   # Stop and remove all containers
   docker-compose down -v
   
   # Remove images
   docker-compose down --rmi all
   
   # Rebuild everything
   docker-compose up --build
   ```

### Logs and Debugging

```bash
# View application logs
docker-compose logs -f app

# View database logs
docker-compose logs -f db

# Check container status
docker-compose ps

# Inspect container
docker-compose exec app php -v
docker-compose exec app apache2 -v
```

## 📁 Project Structure

```
Jonas_docupet/
├── docker-compose.yml          # Docker services configuration
├── Dockerfile                  # PHP/Apache container definition
├── src/                        # Symfony application code
├── templates/                  # Twig templates
├── tests/                      # PHPUnit tests
├── public/                     # Web root
├── var/                        # Cache and logs
└── README_MACOS.md            # This file
```

## ✅ Success Checklist

- [ ] Docker Desktop installed and running
- [ ] Project cloned/downloaded
- [ ] `docker-compose up --build` completed successfully
- [ ] Database migrations applied
- [ ] Fixtures loaded
- [ ] Application accessible at http://localhost:8001
- [ ] Pet registration flow working
- [ ] All tests passing

## 🎉 You're Ready!

Your DocuPet application is now running on macOS with Docker! 

Visit http://localhost:8001 to start registering pets.
