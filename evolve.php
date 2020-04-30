<?php
include __DIR__ . '/vendor/autoload.php';

ini_set('xdebug.max_nesting_level', 3000);

new \ShrinkPress\Evolve\Scan(__DIR__ . '/reduced');
