# Adosia.Market-TX-Builder
>This service runs a full cardano-node wrapped with a custom API, which builds transactions in CDDL format for interactions on https://adosia.market marketplace smart contract and lite wallets.

## Prerequisite
- Linux OS
- Make
- Git
- Docker / Docker-Compose

## Local Install
- Open terminal and type `cd $HOME/Desktop`
- Clone repo `git clone git@github.com:adosia/Adosia.Market-TX-Builder.git`
- Switch to repo dir `cd $HOME/Desktop/Adosia.Market-TX-Builder`
- Copy `docker/.env.example` as `docker/.env`
- Run `make buid` to build & start the containers
- Application should be running locally at `http://0.0.0.0:8030`

## Available Make Commands (Local Development)
* `build` Rebuild all docker containers
* `up` Restart all docker containers
* `down` Shutdown all docker containers
* `status` View the status of all running containers
* `logs` View the logs out of all running containers
* `shell` Drop into an interactive shell inside _dripdropz-web_ container
* `stats` View the resource usage of all running containers
