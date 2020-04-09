<?php

namespace ShrinkPress\Build\Unparse\Task;

use ShrinkPress\Build\Entity\Files\Composer_JSON;
use ShrinkPress\Build\Storage;
use ShrinkPress\Build\Source;

class Wipe extends TaskAbstract
{
	const purge = array(
		'composer.json',
		'.gitignore',
		'.gitattributes',
		);

	function build( Source $source, Storage\StorageAbstract $storage )
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
