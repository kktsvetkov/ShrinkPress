<?php

namespace ShrinkPress\Build\Condense\Task;

use ShrinkPress\Build\Storage;
use ShrinkPress\Build\Condense;
use ShrinkPress\Build\Source;

class Wipe extends TaskAbstract
{
	const purge = array(
		'composer.json',
		'.gitignore',
		'.gitattributes',
		Condense\Compat::compatibility_php,
		);

	function condense(
		Source $source,
		Storage\StorageAbstract $storage
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
