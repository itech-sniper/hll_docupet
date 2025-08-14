# HLL DocuPet

A comprehensive pet registration and management system built with Symfony 6.4, featuring breed-specific safety information, flexible age tracking, and modern responsive design.

## 🚀 Features

- **Pet Registration**: Complete pet registration with name, type, breed, age, and sex
- **Dynamic Breed Selection**: Breed options filtered by pet type (Cat/Dog)
- **Dangerous Animal Detection**: Automatic identification of dangerous breeds with safety warnings
- **Flexible Age Input**: Support for both exact date of birth and approximate age
- **Custom Breed Options**: Handle unknown breeds or mixed breeds
- **Responsive Design**: Modern, mobile-first design using Tailwind CSS
- **Safety Compliance**: Clear danger indicators for regulatory compliance
- **Comprehensive Testing**: Unit tests, integration tests, and data fixtures

## 🛠 Technology Stack

- **Backend**: Symfony 6.4 (PHP 8.2)
- **Frontend**: Symfony UX Live Components, Tailwind CSS
- **Database**: SQLite (default) / MySQL 8.0 (Docker)
- **Testing**: PHPUnit
- **Containerization**: Docker & Docker Compose
- **Package Management**: Composer (PHP), NPM (JavaScript)

## 📋 Requirements

- Docker Desktop (for macOS)
- Git

## 🚀 Quick Start (macOS)

### 1. Clone the Repository

```bash
git clone https://github.com/itech-sniper/hll_docupet.git
cd hll_docupet
```

### 2. Set Up Environment

```bash
# Copy environment file and configure
cp .env.example .env

# Edit .env file if needed (default SQLite configuration should work)
# For Docker MySQL, uncomment the MySQL DATABASE_URL line
```

### 3. Start the Application

```bash
# Build and start all services
docker-compose up --build -d

# Wait for services to be ready (about 30-60 seconds)
docker-compose logs -f app
```

### 4. Set Up the Database

```bash
# Create database and run migrations
docker-compose exec app php bin/console doctrine:database:create --if-not-exists
docker-compose exec app php bin/console doctrine:migrations:migrate --no-interaction

# Load sample data (pet types and breeds)
docker-compose exec app php bin/console doctrine:fixtures:load --no-interaction
```

### 5. Build Frontend Assets

```bash
# Install dependencies and build assets
docker-compose exec app npm install
docker-compose exec app npm run build
```

### 6. Access the Application

- **Main Application**: http://localhost:8001
- **Database**: localhost:3307
  - Username: `root`
  - Password: `root`

## 📖 Usage Guide

### Registering a Pet

1. Navigate to http://localhost:8001
2. Click "Register Your Pet"
3. Fill out the registration form:
   - **Pet's Name**: Enter your pet's name
   - **Pet Type**: Select Cat or Dog
   - **Breed**: Choose from the filtered breed list or select "Can't find it?"
   - **Age**: Choose whether you know the exact birth date or approximate age
   - **Sex**: Select Male, Female, or Unknown
4. Click "Save Pet" to complete registration
5. View the pet summary with all details and safety indicators

### Viewing Registered Pets

1. Click "Pet List" in the navigation
2. Browse all registered pets with summary information
3. Click "View Details" on any pet card to see full information

## 🏗 Architecture & Design

### Database Schema

The application uses a normalized database design:

- **PetType**: Stores pet types (Cat, Dog)
- **Breed**: Stores breeds linked to pet types with danger flags
- **Pet**: Main entity storing all pet information

### MVC Pattern

- **Controllers**: Handle HTTP requests and responses
- **Services**: Contain business logic (PetService)
- **Repositories**: Handle data access
- **Entities**: Represent database models

### Frontend Architecture

- **Responsive Design**: Mobile-first approach with Tailwind CSS
- **Progressive Enhancement**: JavaScript enhances the base HTML experience
- **Accessibility**: Proper form labels, ARIA attributes, and keyboard navigation

## 🧪 Testing

### Running Tests

```bash
# Run all tests
docker-compose exec app php bin/phpunit

# Run specific test suites
docker-compose exec app php bin/phpunit tests/Entity/
docker-compose exec app php bin/phpunit tests/Service/
docker-compose exec app php bin/phpunit tests/Controller/
```

### Test Coverage

- **Entity Tests**: Test business logic and data validation
- **Service Tests**: Test business operations and data processing
- **Controller Tests**: Test HTTP endpoints and API responses

## 🔧 Development

### Local Development Setup

```bash
# Start development environment
docker-compose up -d

# Watch for asset changes (in a separate terminal)
docker-compose exec app npm run watch

# View logs
docker-compose logs -f app

# Access container shell
docker-compose exec app bash
```

### Database Operations

```bash
# Create new migration
docker-compose exec app php bin/console make:migration

# Run migrations
docker-compose exec app php bin/console doctrine:migrations:migrate

# Reset database (development only)
docker-compose exec app php bin/console doctrine:database:drop --force
docker-compose exec app php bin/console doctrine:database:create
docker-compose exec app php bin/console doctrine:migrations:migrate --no-interaction
docker-compose exec app php bin/console doctrine:fixtures:load --no-interaction
```

### Code Quality

```bash
# Check code style (if PHP CS Fixer is installed)
docker-compose exec app vendor/bin/php-cs-fixer fix --dry-run

# Run static analysis (if PHPStan is installed)
docker-compose exec app vendor/bin/phpstan analyse
```

## 🚨 Safety Features

### Dangerous Breed Detection

The system automatically identifies dangerous breeds:

- **Dogs**: Pitbull, Mastiff, American Staffordshire Terrier, Doberman Pinscher
- **Visual Indicators**: Warning badges and detailed notices
- **Compliance**: Helps ensure regulatory compliance

### Data Validation

- Required field validation
- Age range validation (0-50 years)
- Sex enumeration validation
- Breed-type consistency validation

## 🐳 Docker Services

- **app**: Main Symfony application (PHP 8.2-FPM)
- **db**: MySQL 8.0 database
- **phpmyadmin**: Database administration interface

## 📁 Project Structure

```
├── src/
│   ├── Controller/     # HTTP controllers
│   ├── Entity/         # Database entities
│   ├── Repository/     # Data access layer
│   ├── Service/        # Business logic
│   └── DataFixtures/   # Sample data
├── templates/          # Twig templates
├── tests/              # Test suites
├── assets/             # Frontend assets
├── public/             # Web root
├── docker-compose.yml  # Docker configuration
├── Dockerfile          # Application container
└── tailwind.config.js  # Tailwind CSS configuration
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## 📝 License

This project is proprietary software developed for HLL DocuPet.

## 🆘 Troubleshooting

### Common Issues

**Port conflicts**: If ports 8001 or 3307 are in use:
```bash
# Stop the containers
docker-compose down

# Edit docker-compose.yml to use different ports
# Then restart
docker-compose up -d
```

**Database connection issues**:
```bash
# Restart database service
docker-compose restart db

# Check database logs
docker-compose logs db
```

**Asset build issues**:
```bash
# Clear npm cache and reinstall
docker-compose exec app rm -rf node_modules package-lock.json
docker-compose exec app npm install
docker-compose exec app npm run build
```

### Getting Help

- Check the application logs: `docker-compose logs app`
- Check database logs: `docker-compose logs db`
- Verify all services are running: `docker-compose ps`

## 📞 Support

For technical support or questions about this implementation, please contact the development team.
