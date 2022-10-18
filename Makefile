export COMPOSE_PROJECT_NAME=adosiamarket
export COMPOSE_FILE=docker/docker-compose.yml

include ./docker/.env
export

.PHONY: up
up:
	$(MAKE) down
	docker-compose up -d

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

.PHONY: cnode-shell
cnode-shell:
	docker exec -it adosia-market-cardano-node bash

.PHONY: cnode-tip
cnode-tip:
	@docker exec -it adosia-market-cardano-node bash -c "/scripts/query-tip.sh ${ADOSIA_MARKET_NETWORK}"

.PHONY: stats
stats:
	docker stats adosia-market-cardano-node

.PHONY: logs
logs:
	docker-compose logs -f --tail=100
