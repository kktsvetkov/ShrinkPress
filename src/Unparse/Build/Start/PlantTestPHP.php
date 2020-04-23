<?php

namespace ShrinkPress\Reframe\Unparse\Build\Start;

use ShrinkPress\Reframe\Unparse;
use ShrinkPress\Reframe\Index;

class PlantTestPHP implements Unparse\Build\Task
{
	function build(Unparse\Source $source, Index\Index_Abstract $index )
	{
		$code = $source->read($wp_settings = 'wp-settings.php');

		$code .= "\n/* test-builds-only */"
			. "\ninclude '" . dirname(__FILE__, 5) . "/test-shrinkpress.php';"
			. "\n";

		$source->write($wp_settings, $code );
	}
}
