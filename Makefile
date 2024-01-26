.PHONY: default
default: help

.PHONY: help
help:
	@echo "make help     Show this help message"
	@echo "make dev      Run the plugin in the development WordPress"
	@echo "make lint     Run the code linter(s) and print any warnings"
	@echo "make format   Automatically format code"
	@echo "make sure     Make sure that the formatter, linter, tests, etc all pass"

.PHONY: dev
dev: vendor
	docker compose up

.PHONY: lint
lint: vendor
	composer lint

.PHONY: format
format: vendor
	composer format

.PHONY: sure
sure: lint

vendor: composer.json composer.lock
	composer install
