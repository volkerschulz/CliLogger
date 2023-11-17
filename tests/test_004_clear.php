<?php

use volkerschulz\CliLogger as log;

require_once('../vendor/autoload.php');

log::cls();

log::debug('ONE');

log::debug('TWO');

sleep(5);
log::cls();

log::debug('THREE');

log::debug('FOUR');
