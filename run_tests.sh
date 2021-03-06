#!/bin/bash

cd "$(dirname $0)"

php app/console doctrine:database:drop --env=test --force
php app/console doctrine:database:create --env=test
php app/console doctrine:schema:update --env=test --force
php app/console doctrine:fixtures:load --env=test --no-interaction

./bin/behat $@
