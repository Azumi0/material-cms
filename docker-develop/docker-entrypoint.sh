#!/bin/bash

set -e
set -x

# Show env
sleep 5
printenv
env | grep _ | perl -ne 'print "export $_"' >> /etc/environment

sleep 10
service apache2 restart || true
sleep 10

echo "Waiting for services..."
sh -c 'docker-develop/wait-for-it.sh mysql:3306'

cd protected
sh -c 'composer install'
sh -c './yii migrate/up --interactive=0'

tail -f /var/log/apache2/error.log
