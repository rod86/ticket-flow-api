API=docker compose exec api

.PHONY: help
help: ## List all available Makefile commands
	@awk 'BEGIN {FS = ":.*##"} /^[a-zA-Z\/_-]+:.*?##/ { printf " \033[36m%-10s\033[90m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%/s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)


# DOCKER
.PHONY: build up stop bash

build: ## Build docker containers
	@docker compose build

up: ## Start docker containers
	docker compose up -d

stop: ## Stop docker container
	@docker compose stop

bash: ## Go into docker service shell. Usage: make bash s=php
	@docker compose exec $(s) bash


# PROJECT
.PHONY: install

install: ## install composer dependencies
	@$(API) composer install

autoload: ## Regenerate composer autoload file
	@$(API) composer dump-autoload


# TEST
.PHONY: test/unit test/integration

test: test/unit test/integration ## Execute all tests

test/unit: ## Execute unit tests
	@$(API) bin/phpunit tests/Unit

test/integration: ## Execute integration tests
	@$(API) bin/phpunit tests/Integration

# CODE QUALITY
.PHONY: lint lint/check lint/fix

lint: lint/stan lint/fix lint/check ## Run all quality

lint/check: ## Check coding style
	@$(API) vendor/bin/phpcs

lint/fix: ## Fix coding standards
	@$(API) vendor/bin/phpcbf

lint/stan: ## Run PHPStan static analysis
	@$(API) vendor/bin/phpstan analyse
