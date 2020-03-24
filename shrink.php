<?php
// git@github.com:WordPress/WordPress.git

include __DIR__ . '/vendor/autoload.php';

ini_set('xdebug.max_nesting_level', 3000);

error_reporting(E_ALL);
set_error_handler(function($severity, $message, $file, $line)
{
	throw new ErrorException($message, 0, $severity, $file, $line);
}, E_ALL);

$wp_source = __DIR__ . '/wordpress';
\ShrinkPress\Build\Verbose::level(4);

$storage = new \ShrinkPress\Build\Project\Storage\Old(__DIR__ . '/build');
$source = new \ShrinkPress\Build\Project\Source($wp_source, $storage);
// $source->scan();

$process = new \ShrinkPress\Build\Condense\Process;
$process->condense($source, $storage);
