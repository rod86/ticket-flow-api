# Ticket Flow API

Customer support and helpdesk REST API

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

## Xdebug Setup

Xdebug is bundled in the PHP image and enabled by default.

- Start containers with `make up` (not `docker compose up -d` directly) — on WSL2 it computes the correct client host IP for reaching Windows and exports it as `XDEBUG_CLIENT_HOST`.
- In PhpStorm: 
  - In *Settings* > *PHP* > *Debug*, set the debug port to `9003`.
  - In *Settings* > *PHP* > *Servers*, set up a server with:
    - *Host*: localhost
    - *Port*: 8080
    - Enable `Use path mappings` option and map project to server
      **Example**: `//wsl.localhost/Ubuntu-26.04/home/sergi/projects/ticket-flow-api` -> `/app`
- Start listening for PHP debug connections, set a breakpoint and load a page

