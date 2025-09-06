# Core PHP MVC Project

A lightweight MVC (Model-View-Controller) implementation in core PHP with modern practices and patterns inspired by Laravel.

Initial request was to build a simple MVC REST app written in Core PHP (without any persistent storage) in two hours.
The project ultimately required approximately 20 hours to complete, resulting in a more comprehensive solution.
Development was performed using VS Code and Claude Sonnet 3.5, providing a valuable learning experience.
Xdebug integration was added to facilitate debugging in scenarios where automated tools were insufficient.

## Features

- 🏗️ MVC Architecture
- 🔄 Router with support for dynamic routes
- 💾 In-memory storage with APCu caching
- ✅ Request validation
- 🔒 Advanced validation rules (required, email, unique, min age)
- 🎯 Resource transformation layer
- 🐳 Docker support with PHP-FPM and Nginx
- 🐞 Xdebug integration for debugging

## Project Structure

```
plain_php/
├── docker/
│   ├── Dockerfile
│   └── nginx.conf
├── routes/
│   └── api.php
├── src/
│   ├── Controllers/
│   ├── Models/
│   ├── Requests/
│   ├── Resources/
├── docker-compose.yml
└── index.php
```

## Requirements

- Docker
- Docker Compose
- PHP 8.2+
- APCu extension

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd plain_php
```

2. Start the Docker containers:
```bash
docker-compose up -d
```

3. The application will be available at:
```
http://localhost:8080
```

## API Endpoints

### Users

- **GET** `/users` - List all users
- **GET** `/users/{id}` - Get a specific user
- **POST** `/users` - Create a new user
- **PUT** `/users/{id}` - Update a specific user
- **DELETE** `/users/{id}` - Delete a user

### Create User Example

```bash
curl -X POST http://localhost:8080/users \
  -H "Content-Type: application/json" \
  -d '{
    "firstName": "John",
    "lastName": "Doe",
    "email": "john@example.com",
    "dateOfBirth": "1990-01-01"
  }'
```

## Validation Rules

User creation/update includes the following validations:

- **firstName**: Required, max 128 characters
- **lastName**: Optional, max 128 characters
- **email**: Required, must be valid email, must be unique
- **dateOfBirth**: Required, must be valid date, user must be 18+

## Testing

### Running Tests

Run the test suite using PHPUnit in the Docker container:

```bash
./vendor/bin/phpunit
```

To generate code coverage reports:

```bash
./vendor/bin/phpunit --coverage-text
```

### Latest Code Coverage (as of September 6, 2025)

- Lines: 79.92% (207/259)
- Methods: 70.91% (39/55)
- Classes: 33.33% (4/12)


## Resource Transformation

The API transforms user data through UserResource, adding computed properties:

```json
{
    "data": {
        "id": 1,
        "firstName": "John",
        "lastName": "Doe",
        "email": "john@example.com",
        "dateOfBirth": "1990-01-01",
        "age": 35
    }
}
```

## Debugging

The project includes Xdebug configuration for VS Code. To use:

1. Install PHP Debug extension in VS Code
2. Set breakpoints in your code
3. Start debugging (F5)

## Development

### Project Structure Details

- **Controllers**: Handle HTTP requests and responses
- **Models**: Manage data and business logic
- **Requests**: Handle input validation
- **Resources**: Transform data for API responses
- **Routes**: Define API endpoints

### Key Design Patterns

- Singleton Pattern (StorageManager)
- Repository Pattern (Models)
- ~~Strategy Pattern (Validation)~~
- Resource Transformation (API Resources)

## License

MIT License
