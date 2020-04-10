<?php
include __DIR__ . '/vendor/autoload.php';

ini_set('xdebug.max_nesting_level', 3000);

error_reporting(E_ALL);
set_error_handler(function($severity, $message, $file, $line)
{
	throw new ErrorException($message, 0, $severity, $file, $line);
}, E_ALL);

/////////////////////////////////////////////////////////////////////////////

use ShrinkPress\Build;

$wp_source = __DIR__ . '/wordpress';
Build\Verbose::level(4);

$entity_source = Build\Entity\Source::instance();
$entity_source->setSource(
	new Build\Assist\Umbrella($wp_source)
	);

$entity_stash = \ShrinkPress\Build\Entity\Stash::instance();
$entity_stash->setStash(
	new \ShrinkPress\Build\Assist\Umbrella(__DIR__ . '/entities')
	);

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
	if (in_array('clean', $argv))
	{
		$storage->clean();
	}

	$scanner = new \ShrinkPress\Build\Parse\Scanner($source, $storage);
	$scanner->scanFolder('wp-includes/');
}

if (in_array('build', $argv))
{
	$process = new \ShrinkPress\Build\Unparse\Builder;
	$process->build($source, $storage);
}

/////////////////////////////////////////////////////////////////////////////

// register_shutdown_function(function()
// {
// 	echo '<pre style="background:khaki">';
// 	print_r(get_included_files());
// });
