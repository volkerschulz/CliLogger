<?php

use volkerschulz\CliLogger as log;

require_once('../vendor/autoload.php');

log::handleExceptions(true);
log::handleErrors(E_ALL);

trigger_error("SOMETHING WENT WRONG!", E_USER_NOTICE);
trigger_error("SOMETHING WENT WRONG!", E_USER_WARNING);
//trigger_error("SOMETHING WENT WRONG!", E_USER_ERROR);

errorInsideFunction();

function errorInsideFunction() {
    echo $test;
    $test = [];
    echo $test['undefined_array_key'];
    echo $test[0];
    $y = 0;
    return 10 / $y;
}