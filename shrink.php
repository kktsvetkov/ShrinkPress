<?php
// git@github.com:WordPress/WordPress.git

include __DIR__ . '/vendor/autoload.php';

ini_set('xdebug.max_nesting_level', 3000);

error_reporting(E_ALL);
set_error_handler(function($severity, $message, $file, $line)
{
	throw new ErrorException($message, 0, $severity, $file, $line);
}, E_ALL);

// new \ShrinkPress\Build\Captain;
// $d = new \ShrinkPress\Build\Disintegrator;

\ShrinkPress\Build\Verbose::level(4);

$wp_source = __DIR__ . '/wordpress';
$p = new \ShrinkPress\Build\Project($sp_build = __DIR__ . '/build');
// $s = new \ShrinkPress\Build\Scout($p);
// $s->scan($wp_source);

$d = new \ShrinkPress\Build\Doctor($p);
$d->shrink($wp_source);
