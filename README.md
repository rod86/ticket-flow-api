# Pulse Ops

A REST API for small engineering teams to manage production incidents.

## Tech Stack

- PHP 8.5
- Symfony 7.4
- FrankenPHP

## Requirements

- Docker and Docker Compose
- Make

## Getting Started

Build and start the containers:

```bash
make build
make up
```

Install PHP dependencies:

```bash
make install
```

The app will be available at:

- http://localhost:8080

## Makefile Commands

- `make help`: List all available commands
- `make build`: Build Docker containers
- `make up`: Start Docker containers
- `make stop`: Stop Docker containers
- `make bash`: Open a shell in a service container. Example: `make bash s=php`
- `make install`: Install Composer dependencies
- `make autoload`: Regenerate the Composer autoload file
