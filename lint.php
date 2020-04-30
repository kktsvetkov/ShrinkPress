<?php
include __DIR__ . '/vendor/autoload.php';
chdir(__DIR__ . '/reduced');
\ShrinkPress\Evolve\Linter::$ok = '*';
\ShrinkPress\Evolve\Linter::all();
echo "\n";
