.PHONY: help
help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: up
up: ## Start containers
	docker-compose up -d

.PHONY: down
down: ## Stop containers
	docker-compose down

.PHONY: build
build: ## Build containers
	docker-compose build --no-cache

.PHONY: install
install: ## Install dependencies
	docker exec fruits-and-vegetables-challenge-php composer install --optimize-autoloader

.PHONY: update
update: ## Update dependencies
	docker exec fruits-and-vegetables-challenge-php composer update

.PHONY: test
test: ## Run tests
	docker exec fruits-and-vegetables-challenge-php php bin/phpunit

.PHONY: test-coverage
test-coverage: ## Run tests with coverage
	docker exec fruits-and-vegetables-challenge-php php bin/phpunit --coverage-html var/coverage

.PHONY: load-data
load-data: ## Load sample data
	docker exec fruits-and-vegetables-challenge-php php bin/console app:load-data

.PHONY: cache-clear
cache-clear: ## Clear cache
	docker exec fruits-and-vegetables-challenge-php php bin/console cache:clear
	docker exec fruits-and-vegetables-challenge-php php bin/console cache:warmup

.PHONY: logs
logs: ## Show logs
	docker-compose logs -f

.PHONY: shell
shell: ## Enter PHP container
	docker exec -it fruits-and-vegetables-challenge-php bash

.PHONY: setup
setup: down build install cache-clear load-data ## Complete setup
	@echo "✅ Setup complete! Access at http://localhost:8080"

.PHONY: reset
reset: down ## Reset everything
	docker system prune -f
	docker volume rm fruits_vegetables_vendor || true
	rm -rf vendor var/cache var/log

.PHONY: check-requirements
check-requirements: ## Check PHP extensions
	docker exec fruits-and-vegetables-challenge-php php -m | grep -E "(xml|dom|zip|mbstring|intl)"

.PHONY: fix-permissions
fix-permissions: ## Fix Docker permission issues
	@echo "Fixing permissions..."
	@USER_ID=$$(id -u) GROUP_ID=$$(id -g) docker-compose up -d --build
	@docker-compose exec --user root php chown -R $$(id -u):$$(id -g) /var/www/html
	@echo "✅ Permissions fixed"

.PHONY: composer-install-root
composer-install-root: ## Install composer dependencies as root
	docker-compose exec --user root php composer install --no-interaction

.PHONY: clean-install
clean-install: ## Clean install with permission fix
	sudo rm -rf vendor/ var/ composer.lock node_modules/
	make fix-permissions
	make composer-install-root
	docker-compose exec php php bin/console cache:clear