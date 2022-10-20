#!/bin/bash

set -m

sudo ${HOME}/cardano-node/bin/cardano-node run \
  --topology ${HOME}/cardano-node/config/topology.json \
  --database-path ${HOME}/cardano-node/db \
  --socket-path ${HOME}/cardano-node/db/node.socket \
  --host-addr 0.0.0.0 \
  --port 3001 \
  --config ${HOME}/cardano-node/config/config.json &

apache2-foreground

fg %1
