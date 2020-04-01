<?php

namespace ShrinkPress\Build\Condense\Task;

use ShrinkPress\Build\Storage;
use ShrinkPress\Build\Condense;
use ShrinkPress\Build\Verbose;
use ShrinkPress\Build\Source;

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

	 	$all = $storage->getFunctions();
		foreach ($all as $name)
		{
			$entity = $storage->readFunction($name);

			if (empty($entity->fileOrigin))
			{
				Verbose::log("No file: {$entity->name}()", 3);
				continue;
			}

			// build a map of who is replacing who
			//
			self::$replace[ $name ] =
				'\\' . $entity->classNamespace . $entity->className
				. '::' . $entity->classMethod;


			// build a map of who is calling who
			//
			if (empty(static::$map[ $name ]))
			{
				static::$map[ $name ] = array();
			}

			foreach ($entity->callers as $call)
			{
                               if (empty($call[2]))
                               {
                                       continue;
                               }

                               $func = $call[2];
                               if (empty( self::$map[ $func ] ))
                               {
                                       self::$map[ $func ] = array();
                               }

                               self::$map[ $func ][ $call[1] ][] = $name;
			}
		}
	}
}
