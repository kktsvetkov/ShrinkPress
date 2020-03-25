<?php
include __DIR__ . '/vendor/autoload.php';

ini_set('xdebug.max_nesting_level', 3000);

error_reporting(E_ALL);
set_error_handler(function($severity, $message, $file, $line)
{
	throw new ErrorException($message, 0, $severity, $file, $line);
}, E_ALL);

$wp_source = __DIR__ . '/wordpress';
\ShrinkPress\Build\Verbose::level(4);

$storage = new \ShrinkPress\Build\Project\Storage\MySQL(
	new mysqli('127.0.0.1', 'username', 'password', 'wordpress')
	);
// $storage = new \ShrinkPress\Build\Project\Storage\Dummy;
// $storage = new \ShrinkPress\Build\Project\Storage\Stash(__DIR__ . '/build');

$source = new \ShrinkPress\Build\Project\Source($wp_source, $storage);

if (in_array('scan', $argv))
{
	$source->scan();
}

if (in_array('process', $argv))
{
	$process = new \ShrinkPress\Build\Condense\Process;
	$process->condense($source, $storage);
}
