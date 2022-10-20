#!/usr/bin/make

export COMPOSE_PROJECT_NAME=adosiamarket
export COMPOSE_FILE=docker/docker-compose.yml

include ./docker/.env
export

SHELL = /bin/sh
CURRENT_UID := $(shell id -u)
CURRENT_GID := $(shell id -g)
export CURRENT_UID
export CURRENT_GID

.PHONY: up
up:
	$(MAKE) down
	docker-compose up -d
	$(MAKE) composer-install
	$(MAKE) status

.PHONY: down
down:
	docker-compose down --remove-orphans

.PHONY: build
build:
	docker-compose build
	$(MAKE) up

.PHONY: rebuild
rebuild:
	docker-compose build --no-cache
	$(MAKE) up

.PHONY: status
status:
	docker-compose ps

.PHONY: shell
shell:
	docker exec -it adosia-market-tx-builder-web bash

.PHONY: stats
stats:
	docker stats adosia-market-tx-builder-web

.PHONY: logs
logs:
	docker-compose logs -f --tail=100

.PHONY: composer-install
composer-install:
	docker exec -it adosia-market-tx-builder-web bash -c "composer install"
