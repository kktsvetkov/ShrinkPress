<?php
include __DIR__ . '/vendor/autoload.php';

ini_set('xdebug.max_nesting_level', 3000);

use ShrinkPress\Reframe;

new Reframe\Evolve\Scan(__DIR__ . '/reduced');
