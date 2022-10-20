#!/bin/bash

set -m

${HOME}/cardano-node/bin/cardano-node run \
  --topology ${HOME}/cardano-node/config/topology.json \
  --database-path ${HOME}/cardano-node/db \
  --socket-path ${HOME}/cardano-node/db/node.socket \
  --host-addr 0.0.0.0 \
  --port 6000 \
  --config ${HOME}/cardano-node/config/config.json &

apache2-foreground

fg %1
