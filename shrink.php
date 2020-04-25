<?php
include __DIR__ . '/vendor/autoload.php';

ini_set('xdebug.max_nesting_level', 3000);

error_reporting(E_ALL);
set_error_handler(function($severity, $message, $file, $line)
{
	throw new ErrorException($message, 0, $severity, $file, $line);
}, E_ALL);

/////////////////////////////////////////////////////////////////////////////

use ShrinkPress\Reframe;

$wp_source = __DIR__ . '/wordpress';
Reframe\Assist\Verbose::level(4);

$index = new Reframe\Index\Index_Stash(
	new Reframe\Assist\Umbrella(__DIR__ . '/entities')
	);

if (in_array('scan', $argv))
{
	if (in_array('clean', $argv))
	{
		$index->clean();
	}

	$source = new Reframe\Parse\Source(__DIR__ . '/wordpress');
	$scanner = new Reframe\Parse\Scanner($source, $index);
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
	$source = new Reframe\Unparse\Source(__DIR__ . '/reduced');

	$modified = new Reframe\Index\Index_Stash(
		new Reframe\Assist\Umbrella(__DIR__ . '/modified')
		);

	$process = new Reframe\Unparse\Builder;
	$process->build($source, $index);
}
