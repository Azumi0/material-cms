FROM nimmis/apache-php7
MAINTAINER Przemyslaw Wrzeszcz <przemyslaw.wrzeszcz@gmail.com>

# Copy this repo into place
ADD . /var/www/html
WORKDIR /var/www/html

# Expose node
EXPOSE 80

RUN apt-get update && apt-get install -y php7.0-mbstring php7.0-xml php-xdebug php-curl php7.0-soap && \
    phpenmod mbstring && \
    a2enmod rewrite
RUN cp -f ./project_vhosts /etc/apache2/sites-available/000-default.conf && \
    chmod 777 /etc/apache2/sites-available/000-default.conf && \
    cp -f ./apache2.conf /etc/apache2/apache2.conf && \
    chmod 777 /etc/apache2/apache2.conf && \
    cp -f ./php.ini /etc/php/7.0/apache2/php.ini && \
    chmod 777 /etc/php/7.0/apache2/php.ini && \
    cp -f ./xdebug.ini /etc/php/7.0/mods-available/xdebug.ini && \
    chmod 777 /etc/php/7.0/mods-available/xdebug.ini && \
    cp -f ./envvars /etc/apache2/envvars && \
    chmod 777 /etc/apache2/envvars

# Run provision
CMD ["sh", "./docker-develop/docker-entrypoint.sh"]
