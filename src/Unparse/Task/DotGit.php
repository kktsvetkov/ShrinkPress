<?php

namespace ShrinkPress\Build\Unparse\Task;

use ShrinkPress\Build\File;
use ShrinkPress\Build\Storage;
use ShrinkPress\Build\Source;

class DotGit extends TaskAbstract
{
	const gitignore = array(
		'/composer.lock',
		'/wp-config.php',
		);

	const gitattributes = array();

	function build(Source $source, Storage\StorageAbstract $storage)
	{
		if ($gitignore = join("\n", self::gitignore))
		{
			$source->write('.gitignore', $gitignore );
		}

		if ($gitattributes = join("\n", self::gitattributes))
		{
			$source->write('.gitattributes', $gitattributes );
		}
	}
}
