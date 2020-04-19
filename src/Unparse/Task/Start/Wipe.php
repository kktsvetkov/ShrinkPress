<?php

namespace ShrinkPress\Build\Unparse\Task\Start;

use ShrinkPress\Build\Entity\Files\Composer_JSON;
use ShrinkPress\Build\Unparse;
use ShrinkPress\Build\Index;

class Wipe implements Unparse\Task\Task
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
