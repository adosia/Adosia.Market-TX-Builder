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

.PHONY: status
status:
	docker-compose ps

.PHONY: web-shell
web-shell:
	docker exec -it adosia-market-tx-builder-web bash

.PHONY: nodejs-shell
nodejs-shell:
	docker exec -it adosia-market-tx-builder-nodejs bash

.PHONY: stats
stats:
	docker stats adosia-market-tx-builder-web adosia-market-tx-builder-nodejs

.PHONY: logs
logs:
	docker-compose logs -f --tail=100

.PHONY: composer-install
composer-install:
	docker exec -it adosia-market-tx-builder-web bash -c "composer install"

.PHONY: gLiveView
gLiveView:
	docker exec -it adosia-market-tx-builder-web bash -c "../gLiveView.sh"
