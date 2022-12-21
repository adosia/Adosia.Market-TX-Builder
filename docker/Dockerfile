FROM php:8.1-apache-buster
MAINTAINER Latheesan Kanesamoorthy <latheesan87@gmail.com>

# Build arguments
ARG LINUX_USERNAME
ARG CURRENT_UID
ARG CURRENT_GID
ARG CARDANO_NODE_VERSION
ARG CARDANO_NETWORK
ARG DEBIAN_FRONTEND=noninteractive

# Update base system
RUN apt-get update && \
    apt-get install --no-install-recommends -y wget curl nano sudo libzip-dev zip unzip netbase iproute2 jq bc
RUN curl -fsSL https://deb.nodesource.com/setup_19.x | sudo -E bash -
RUN sudo apt-get install -y nodejs

# Configure php & apache
RUN cp /usr/local/etc/php/php.ini-production php.ini && \
    rm -rf /etc/apache2/sites-available/* && \
    rm -rf /etc/apache2/sites-enabled/* && \
    echo 'ServerName adosiamarket_tx_builder.app' >> /etc/apache2/apache2.conf
COPY /docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY /docker/adosiamarket_tx_builder.app.conf /etc/apache2/sites-available/adosiamarket_tx_builder.app.conf
RUN a2enmod rewrite && \
    a2ensite adosiamarket_tx_builder.app

# Clean-up
RUN sudo apt-get -y purge && sudo apt-get -y clean && \
    sudo apt-get -y autoremove && sudo rm -rf /var/lib/apt/lists/* && \
    sudo rm -rf /usr/bin/apt*

# Create linux user
RUN echo "${LINUX_USERNAME} ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers && \
    groupadd -g ${CURRENT_GID} accounts && \
    adduser --disabled-password --gecos '' ${LINUX_USERNAME} --uid ${CURRENT_UID} --gid ${CURRENT_GID} && \
    usermod -aG sudo ${LINUX_USERNAME}

# Install composer globally
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && rm -f composer-setup.php \
    && mv composer.phar /usr/local/bin/composer

# Set linux user
USER ${LINUX_USERNAME}
WORKDIR /home/${LINUX_USERNAME}/application

# Download & extract cardano node binaries
RUN mkdir -p ~/cardano-node/bin && \
    mkdir ~/cnode-src && \
    cd ~/cnode-src && \
    wget https://update-cardano-mainnet.iohk.io/cardano-node-releases/cardano-node-${CARDANO_NODE_VERSION}-linux.tar.gz && \
    tar -xvf cardano-node-${CARDANO_NODE_VERSION}*.tar.gz && \
    cp cardano-node ~/cardano-node/bin/cardano-node && \
    cp cardano-cli ~/cardano-node/bin/cardano-cli && \
    cp bech32 ~/cardano-node/bin/bech32 && \
    rm -rf ~/cnode-src && \
    echo 'export PATH="$PATH:$HOME/cardano-node/bin"' >> ~/.bashrc

# Download cardano node configurations
RUN mkdir -p ~/cardano-node/config && \
    wget -O ~/cardano-node/config/config.json https://book.world.dev.cardano.org/environments/${CARDANO_NETWORK}/config.json && \
    wget -O ~/cardano-node/config/byron-genesis.json https://book.world.dev.cardano.org/environments/${CARDANO_NETWORK}/byron-genesis.json && \
    wget -O ~/cardano-node/config/shelley-genesis.json https://book.world.dev.cardano.org/environments/${CARDANO_NETWORK}/shelley-genesis.json && \
    wget -O ~/cardano-node/config/alonzo-genesis.json https://book.world.dev.cardano.org/environments/${CARDANO_NETWORK}/alonzo-genesis.json && \
    wget -O ~/cardano-node/config/topology.json https://book.world.dev.cardano.org/environments/${CARDANO_NETWORK}/topology.json

# Install & configure gLiveView \
RUN wget -O ~/env https://github.com/cardano-community/guild-operators/raw/master/scripts/cnode-helper-scripts/env && \
    wget -O ~/gLiveView.sh https://github.com/cardano-community/guild-operators/raw/master/scripts/cnode-helper-scripts/gLiveView.sh && \
    chmod +x ~/gLiveView.sh && \
    sed -i 's/#CNODEBIN="${HOME}\/.cabal\/bin\/cardano-node"/CNODEBIN="${HOME}\/cardano-node\/bin\/cardano-node"/g' ~/env && \
    sed -i 's/#CCLI="${HOME}\/.cabal\/bin\/cardano-cli"/CCLI="${HOME}\/cardano-node\/bin\/cardano-cli"/g' ~/env && \
    sed -i 's/#CNODE_PORT=6000/CNODE_PORT=6000/g' ~/env && \
    sed -i 's/#CNODE_HOME="\/opt\/cardano\/cnode"/CNODE_HOME="${HOME}\/cardano-node"/g' ~/env && \
    sed -i 's/#CONFIG="${CNODE_HOME}\/files\/config.json"/CONFIG="${HOME}\/cardano-node\/config\/config.json"/g' ~/env && \
    sed -i 's/#SOCKET="${CNODE_HOME}\/sockets\/node0.socket"/SOCKET="${HOME}\/cardano-node-data\/db\/node.socket"/g' ~/env && \
    sed -i 's/#TOPOLOGY="${CNODE_HOME}\/files\/topology.json"/TOPOLOGY="${CNODE_HOME}\/config\/topology.json"/g' ~/env

# Install entrypoint script
COPY /docker/entrypoint.sh /home/${LINUX_USERNAME}/entrypoint.sh

# Expose apache port
EXPOSE 80
EXPOSE 6000

# Run Cardano node and Apache2
CMD ${HOME}/entrypoint.sh
