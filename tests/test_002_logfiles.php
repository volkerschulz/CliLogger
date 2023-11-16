<?php

use volkerschulz\CliLogger as log;

require_once('../vendor/autoload.php');

$test_filename = date("Y_m_d_H_i_s") . '.txt';
log::setLogFile(realpath(__DIR__) . '/output/' . $test_filename);

// OUPUT TO BOTH, CLI AND FILE
log::error('This is an error message');
log::success('This is a success message');
log::warning('This is a warning message');
log::notice('This is a notice');
log::debug('This is a general debug message');
log::print('This is a general message');
log::print(''); // New line
// NON-STRING
$arr = ['foo' => 'bar'];
log::debug($arr);

// OUTPUT TO FILE ONLY
log::setOptions(['cli_muted' => true]);
log::error('This is an error message');
log::success('This is a success message');
log::warning('This is a warning message');
log::notice('This is a notice');
log::debug('This is a general debug message');
log::print('This is a general message');
log::print(''); // New line
// NON-STRING
$arr = ['foo' => 'bar'];
log::debug($arr);