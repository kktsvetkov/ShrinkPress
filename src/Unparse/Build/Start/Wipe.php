<?php

namespace ShrinkPress\Reframe\Unparse\Build\Start;

use ShrinkPress\Reframe\Entity\Files\Composer_JSON;
use ShrinkPress\Reframe\Unparse;
use ShrinkPress\Reframe\Index;

class Wipe implements Unparse\Build\Task
{
	const purge = array(
		'composer.json',
		'.gitignore',
		'.gitattributes',
		);

	function build( Unparse\Source $source, Index\Index_Abstract $index )
	{
		foreach (self::purge as $file)
		{
			if ($source->exists($file))
			{
				$source->unlink($file);
			}
		}

		chdir( $source->basedir() );
		shell_exec('git checkout -- .');

		shell_exec('rm -rf ' . Composer_JSON::vendors);
	}
}
