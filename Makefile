PHP=docker compose exec php

.PHONY: help
help: ## List all available Makefile commands
	@awk 'BEGIN {FS = ":.*##"} /^[a-zA-Z\/_-]+:.*?##/ { printf " \033[36m%-10s\033[90m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%/s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)


# DOCKER
.PHONY: build up stop bash
build: ## Build docker containers
	@docker compose build

up: ## Start docker containers
	@docker compose up -d

stop: ## Stop docker container
	@docker compose stop

bash: ## Go into docker service shell. Usage: make bash s=php
	@docker compose exec $(s) bash


# PROJECT

.PHONY: install autoload
install: ## install composer dependencies
	@$(PHP) composer install

autoload: ## Regenerate composer autoload file
	@$(PHP) composer dump-autoload


# TEST

.PHONY: test/unit test/integration

test: test/unit test/integration ## Execute all tests

test/unit: ## Execute unit tests
	@$(PHP) bin/phpunit tests/Unit

test/integration: ## Execute integration tests
	@$(PHP) bin/phpunit tests/Integration
