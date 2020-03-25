<?php

namespace ShrinkPress\Build\Condense\Task;

use ShrinkPress\Build\Project;
use ShrinkPress\Build\Condense;

class Functions extends TaskAbstract
{
	function condense(
		Project\Source $source,
		Project\Storage\StorageAbstract $storage
		)
	{
		$packages = Condense\Packages::instance();
		foreach( $packages->getPackages() as $name)
		{
			$def = $packages->definition( $name );
			print_r($def);
		}
	}
}
