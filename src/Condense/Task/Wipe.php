<?php

namespace ShrinkPress\Build\Condense\Task;

use ShrinkPress\Build\Project;
use ShrinkPress\Build\Condense;

class Wipe extends TaskAbstract
{
	const purge = array(
		'composer.json',
		'.gitignore',
		'.gitattributes',
		Condense\Compat::compatibility_php,
		);

	function condense(
		Project\Source $source,
		Project\Storage\StorageAbstract $storage
		)
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

		shell_exec('rm -rf ' . Condense\Composer::vendors);
	}
}
