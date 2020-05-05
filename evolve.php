<?php
include __DIR__ . '/vendor/autoload.php';

ini_set('xdebug.max_nesting_level', 3000);

$p = new \ShrinkPress\Evolve\Project(__DIR__ . '/reduced');
$p->run();
