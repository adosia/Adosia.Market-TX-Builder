#!/bin/bash

echo "Network: $1"

case $1 in

  mainnet)
    cardano-cli query tip
    ;;

  preprod)
    cardano-cli query tip --testnet-magic 1
    ;;

  preview)
    cardano-cli query tip --testnet-magic 2
    ;;

esac
