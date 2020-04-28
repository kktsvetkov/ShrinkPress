<?php
include __DIR__ . '/vendor/autoload.php';
chdir(__DIR__ . '/reduced');
\ShrinkPress\Reframe\Evolve\Linter::$ok = '*';
\ShrinkPress\Reframe\Evolve\Linter::all();
echo "\n";
