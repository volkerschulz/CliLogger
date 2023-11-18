<?php

use volkerschulz\CliLogger as log;

require_once('../vendor/autoload.php');

log::handleExceptions(true);
//throw New Exception('Oh no!');
//log::catchExceptions(false);
exceptionInsideFunction();

function exceptionInsideFunction() {
    throw New Exception('Oh no!');
}