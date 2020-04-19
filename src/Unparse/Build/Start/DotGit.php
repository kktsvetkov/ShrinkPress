<?php

namespace ShrinkPress\Reframe\Unparse\Build\Start;

use ShrinkPress\Reframe\Unparse;
use ShrinkPress\Reframe\Index;

class DotGit implements Unparse\Build\Task
{
	const gitignore = array(
		'/composer.lock',
		'/wp-config.php',
		);

	const gitattributes = array();

	function build(Unparse\Source $source, Index\Index_Abstract $index )
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
