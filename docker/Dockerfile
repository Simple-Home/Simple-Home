FROM webdevops/php-apache-dev:7.4

RUN apt-get update && apt-get upgrade -y && apt-get install -y sudo systemd w3m mariadb-client supervisor
RUN systemctl enable apache2

# install nodejs
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash
RUN apt-get update && apt-get install -y nodejs
RUN npm install -g npm@latest

COPY ./start.sh /home/application/start.sh
RUN chmod +x /home/application/start.sh

COPY ./supervisor.conf /etc/supervisord.conf

RUN echo xdebug.log = /app/storage/logs/xdebug.log >> /opt/docker/etc/php/php.ini


RUN echo 'application ALL=(ALL) NOPASSWD:/usr/local/bin/service apache2 status, /usr/local/bin/service mysql status' >> /etc/sudoers
WORKDIR /app
