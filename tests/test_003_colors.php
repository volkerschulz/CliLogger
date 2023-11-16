<?php

use volkerschulz\CliLogger as log;

require_once('../vendor/autoload.php');

log::setOptions(['format' => ['d' => ['yellow']]]);
log::debug('This is a general debug message');

log::setOptions(['format' => ['d' => ['blue']]]);
log::debug('This is a general debug message');

log::setOptions(['format' => ['d' => ['magenta']]]);
log::debug('This is a general debug message');

log::setOptions(['format' => ['d' => ['cyan']]]);
log::debug('This is a general debug message');

log::setOptions(['format' => ['d' => ['white']]]);
log::debug('This is a general debug message');

log::setOptions(['format' => ['d' => ['cyan', 'redbg']]]);
log::debug('This is a general debug message');

log::setOptions(['format' => ['d' => ['black', 'greenbg']]]);
log::debug('This is a general debug message');

log::setOptions(['format' => ['d' => ['black', 'yellowbg']]]);
log::debug('This is a general debug message');

log::setOptions(['format' => ['d' => ['yellow', 'bluebg']]]);
log::debug('This is a general debug message');

log::setOptions(['format' => ['d' => ['black', 'magentabg']]]);
log::debug('This is a general debug message');

log::setOptions(['format' => ['d' => ['black', 'cyanbg']]]);
log::debug('This is a general debug message');

log::setOptions(['format' => ['d' => ['red', 'lightgreybg']]]);
log::debug('This is a general debug message');

log::setOptions(['format' => ['d' => ['yellow', 'bold']]]);
log::debug('This is a general debug message');

log::setOptions(['format' => ['d' => ['blue', 'italic']]]);
log::debug('This is a general debug message');

log::setOptions(['format' => ['d' => ['magenta', 'underline', 'bold']]]);
log::debug('This is a general debug message');

log::setOptions(['format' => ['d' => ['cyan', 'strikethrough']]]);
log::debug('This is a general debug message');
