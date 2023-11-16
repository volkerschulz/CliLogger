<?php

use volkerschulz\CliLogger as log;

require_once('../vendor/autoload.php');

// USING THE LOG FUNCTION 
log::log('This is an error message', 'e'); // Log test error
log::log('This is a success message', 's'); // Log test success
log::log('This is a warning message', 'w'); // Log test warning
log::log('This is a notice', 'n'); // Log test notice
log::log('This is a general debug message', 'd'); // Log debug message (no prefix)
log::log('This is a general message'); // Log test general
log::log('This is another general message'); // Log test general
log::log(''); // New line

// Setting options
log::setOptions(['use_file_format_for_cli' => true]);

// USING DEDICATED FUNCTIONS
log::error('This is an error message');
log::success('This is a success message');
log::warning('This is a warning message');
log::notice('This is a notice');
log::debug('This is a general debug message');
log::print('This is a general message');
log::print(''); // New line

// NON-STRINGS
$arr = ['foo' => 'bar'];
log::debug($arr);

$ch = curl_init();
log::print($ch);
