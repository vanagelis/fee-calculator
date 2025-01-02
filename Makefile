.PHONY: init
init: docker-build composer-install

.PHONY: start
start: docker-up

.PHONY: tests
tests: tests

docker-build docker-up:
	@docker-compose $(CMD)

docker-build: CMD=up -d --build
docker-up: CMD=up -d

.PHONY: composer
composer composer-install:
	docker compose exec php composer $(CMD)

composer-install: CMD=install

tests:
	docker compose exec php $(CMD)
tests: CMD=bin/phpunit
