<?php

namespace ShrinkPress\Build\Condense\Task;

use ShrinkPress\Build\Project\Storage;
use ShrinkPress\Build\Project\Source;
use ShrinkPress\Build\Condense;
use ShrinkPress\Build\Verbose;

class SortFunctions extends TaskAbstract
{
	static $map = [];
	static $replace = [];

	function condense(
		Source $source,
		Storage\StorageAbstract $storage
		)
	{
		Verbose::log('Building functions map...', 2);

		// build map of who is calling who
		//
	 	$all = $storage->getFunctions();
		foreach ($all as $name)
		{
			$entity = $storage->readFunction($name);

			if (empty($entity->fileOrigin))
			{
				Verbose::log("No file: {$entity->name}()", 3);
				continue;
			}

			self::$replace[ $name ] =
				'\\' . $entity->classNamespace . $entity->className
				. '::' . $entity->classMethod;

			if (empty( self::$map[ $name ] ))
			{
				self::$map[ $name ] = array();
			}

			foreach($entity->callers as $call)
			{
				if (empty($call[2]))
				{
					continue;
				}

				$func = $call[2];
				if (empty( self::$map[ $func ][ $name ] ))
				{
					self::$map[ $func ][ $name ] = array();
				}

				self::$map[ $func ][ $name ][] = $call[1];
			}
		}

		// save a map
		//
		// $source->write(
		// 	Condense\Composer::vendors . '/shrinkpress/map.functions.json',
		// 	json_encode( self::$map, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES )
		// 	);
	}
}
