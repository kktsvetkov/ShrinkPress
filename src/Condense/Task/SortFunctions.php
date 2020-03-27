<?php

namespace ShrinkPress\Build\Condense\Task;

use ShrinkPress\Build\Project;
use ShrinkPress\Build\Condense;

class SortFunctions extends TaskAbstract
{
	function condense(
		Project\Source $source,
		Project\Storage\StorageAbstract $storage
		)
	{
		$map = array();
		foreach ($storage->getFunctions() as $name)
		{
			$entity = $storage->readFunction($name);
			if (empty($map[ $name ]))
			{
				$map[ $name ] = 0;
			}

			foreach($entity->callers as $call)
			{
				if (empty($call[2]))
				{
					continue;
				}

				if (empty( $map[ $call[2] ] ))
				{
					$map[ $call[2] ] = 1;
				} else
				{
					$map[ $call[2] ] += 1;
				}
			}
		}

		asort($map);
		$storage->sortedFunctions = $map;

		foreach ($map as $name => $calls)
		{
			$entity = $storage->readFunction($name);
			$entity->calls = $calls;
			$storage->writeFunction($entity);
		}
	}
}
