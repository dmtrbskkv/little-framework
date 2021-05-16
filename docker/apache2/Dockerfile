FROM php:7.4-apache-buster

ENV USER=user USER_ID=1000 USER_GID=1000

RUN a2enmod rewrite
RUN a2enmod headers

RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql

#RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
#RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
#RUN php composer-setup.php
#RUN php -r "unlink('composer-setup.php');"

RUN addgroup --gid ${USER_GID} ${USER}
RUN adduser --disabled-password --gecos '' --uid ${USER_ID} --gid ${USER_GID} ${USER}
USER ${USER}

