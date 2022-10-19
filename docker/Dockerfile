FROM php:8.1-apache-buster
MAINTAINER Latheesan Kanesamoorthy <latheesan87@gmail.com>

# Update base system
ARG DEBIAN_FRONTEND=noninteractive
RUN apt-get update && \
    apt-get install --no-install-recommends -y wget curl nano sudo libzip-dev zip unzip

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

# Create adosiamarket_tx_builder user
RUN adduser --disabled-password --gecos '' adosiamarket_tx_builder && \
    echo '%sudo ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers && \
    adduser adosiamarket_tx_builder sudo && \
    chown -R adosiamarket_tx_builder:adosiamarket_tx_builder /home/adosiamarket_tx_builder/.*

# Install composer globally
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && rm -f composer-setup.php \
    && mv composer.phar /usr/local/bin/composer

# Set adosiamarket_tx_builder user
USER adosiamarket_tx_builder
WORKDIR /home/adosiamarket_tx_builder/application

# Expose apache port
EXPOSE 80
