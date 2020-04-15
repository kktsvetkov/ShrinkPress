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
Build\Assist\Verbose::level(4);

$index1 = new Build\Index\Index_PDO(
	new PDO("mysql:host=127.0.0.1;dbname=wordpress;charset=utf8mb4",
		'username',
		'password',
		array(
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_EMULATE_PREPARES => false,
	)));

$index2 = new Build\Index\Index_Stash(
	new Build\Assist\Umbrella(__DIR__ . '/entities')
);

$index3 = new Build\Index\Index_Dummy;

$index4 = new Build\Index\Index_Nested;
$index4->addNested($index1);
$index4->addNested($index2);
$index4->addNested($index3);

$index = $index2;

if (in_array('scan', $argv))
{
	if (in_array('clean', $argv))
	{
		$index->clean();
	}

	$source = new Build\Parse\Source($wp_source);
	$scanner = new Build\Parse\Scanner($source, $index);
	// $scanner->scanFolder('wp-includes/');
	$scanner->scanFolder('');

	echo "Files: ", count( $index->getFiles() ), " found\n";
	echo "Packages: ", count( $index->getPackages() ), " found\n";
	echo "Functions: ", count( $index->getFunctions() ), " found\n";
	echo "Classes: ", count( $index->getClasses() ), " found\n";
	echo "Globals: ", count( $index->getGlobals() ), " found\n";
	echo "Included: ", count( $index->getIncludes() ), " found\n";
}

if (in_array('build', $argv))
{
	$source = new Build\Unparse\Source($wp_source);
	$process = new Build\Unparse\Builder;
	$process->build($source, $index);
}

/////////////////////////////////////////////////////////////////////////////

// register_shutdown_function(function()
// {
// 	echo '<pre style="background:khaki">';
// 	print_r(get_included_files());
// });
