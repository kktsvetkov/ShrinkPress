<?php

namespace ShrinkPress\Build\Condense\Task;

use ShrinkPress\Build\Project;
use ShrinkPress\Build\Condense;

class Start extends TaskAbstract
{
	const gitignore = array(
		'/composer.lock',
		'/wp-config.php',
		);

	const gitattributes = array();

	function condense(
		Project\Source $source,
		Project\Storage\StorageAbstract $storage
		)
	{
		// start with composer.json ...
		//
		$composer = Condense\Composer::instance();
		$source->write('composer.json', $composer->json() );

		// ... then do the dot files
		//
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
