<?php
include __DIR__ . '/vendor/autoload.php';

ini_set('xdebug.max_nesting_level', 3000);

error_reporting(E_ALL);
set_error_handler(function($severity, $message, $file, $line)
{
	throw new ErrorException($message, 0, $severity, $file, $line);
}, E_ALL);

/////////////////////////////////////////////////////////////////////////////

$wp_source = __DIR__ . '/wordpress';
\ShrinkPress\Build\Verbose::level(4);

$storage = new \ShrinkPress\Build\Storage\PDO(
	new PDO("mysql:host=127.0.0.1;dbname=wordpress;charset=utf8mb4",
		'username',
		'password',
		array(
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_EMULATE_PREPARES => false,
	)));

$source = new \ShrinkPress\Build\Source($wp_source);

if (in_array('scan', $argv))
{
	$storage->clean();

	$scanner = new \ShrinkPress\Build\Parse\Scanner($source, $storage);
	$scanner->scanFolder('');
}

if (in_array('process', $argv))
{
	$process = new \ShrinkPress\Build\Condense\Process($source, $storage);
	$process->condense();
}
