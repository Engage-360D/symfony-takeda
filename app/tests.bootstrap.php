<?php

passthru(sprintf(
    'php "%s/console" doctrine:database:drop --env=test --force',
    __DIR__
));

passthru(sprintf(
    'php "%s/console" doctrine:database:create --env=test',
    __DIR__
));

passthru(sprintf(
    'php "%s/console" doctrine:schema:update --env=test --force',
    __DIR__
));

passthru(sprintf(
    'php "%s/console" doctrine:fixtures:load --env=test --no-interaction',
    __DIR__,
    __DIR__
));

require __DIR__.'/bootstrap.php.cache';
