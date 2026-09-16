PHP=docker compose exec php

.PHONY: help
help: ## List all available Makefile commands
	@awk 'BEGIN {FS = ":.*##"} /^[a-zA-Z\/_-]+:.*?##/ { printf " \033[36m%-10s\033[90m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%/s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)


# DOCKER
.PHONY: build up stop bash

build: ## Build docker containers
	@docker compose build

up: ## Start docker containers
	@if grep -qi microsoft /proc/version 2>/dev/null; then \
		export XDEBUG_CLIENT_HOST=$$(ip route show default | awk '{print $$3}'); \
	fi; \
	docker compose up -d

stop: ## Stop docker container
	@docker compose stop

bash: ## Go into docker service shell. Usage: make bash s=php
	@docker compose exec $(s) bash


# PROJECT
.PHONY: install

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


# CODE QUALITY
.PHONY: lint/stan lint/cs lint/fix

lint: lint/stan lint/cs lint/fix ## Run all quality checks

lint/stan: ## Run PHPStan static analysis
	@$(PHP) vendor/bin/phpstan analyse

lint/cs: ## Check coding standards (dry-run)
	@$(PHP) vendor/bin/php-cs-fixer fix --dry-run --diff

lint/fix: ## Fix coding standards
	@$(PHP) vendor/bin/php-cs-fixer fix
