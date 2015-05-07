<?php

require_once __DIR__.'/../autoload.php';

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Portunus\ContainerBuilder;

$ContainerBuilder = new ContainerBuilder();

return ConsoleRunner::createHelperSet($ContainerBuilder->get('doctrine.entity_manager'));
