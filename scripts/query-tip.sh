#!/bin/bash

echo "Network: $1"

case $1 in

  mainnet)
    cardano-cli query tip
    ;;

  preview)
    cardano-cli query tip --testnet-magic 2
    ;;

  preprod)
    cardano-cli query tip --testnet-magic 5
    ;;

esac
