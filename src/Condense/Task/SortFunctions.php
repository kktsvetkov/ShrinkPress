<?php

namespace ShrinkPress\Build\Condense\Task;

use ShrinkPress\Build\Project;
use ShrinkPress\Build\Condense;
use ShrinkPress\Build\Verbose;

class SortFunctions extends TaskAbstract
{
	static $map = [];

	/**
	* Remove a function from the map
	*
	* You can only remove functions that do not call other functions
	*
	* @param string $name
	* @throws \InvalidArgumentException
	*/
	static function remove( $name )
	{
		$name = (string) $name;
		if (!isset( self::$map[ $name ] ))
		{
			throw new \InvalidArgumentException(
				'Not able to find function: '
					. $name . '()'
			);
		}

		if (!empty( self::$map[ $name ] ))
		{
			throw new \InvalidArgumentException(
				'Not able to remove ' . $name . '(), still has calls: '
					. join('(), ', array_keys(self::$map[ $name ]))
					. '()'
			);
		}

		unset(self::$map[ $name ]);
		foreach (self::$map as $func => $calls)
		{
			unset(self::$map[ $func ][ $name ]);
		}
	}

	/**
	* Pop the next batch of functons that do not call other functions
	* @return array
	*/
	static function pop()
	{
		self::sort();

		$result = array();
		foreach (self::$map as $name => $calls)
		{
			if (empty($calls))
			{
				$result[] = $name;
			}
		}

		return $result;
	}

	function condense(
		Project\Source $source,
		Project\Storage\StorageAbstract $storage
		)
	{
		Verbose::log('Building functions map...', 2);

		// restore a map ?
		//
		$map_json = Condense\Composer::vendors . '/shrinkpress/map.functions.json';
		if ($source->exists( $map_json ))
		{
			Verbose::log('Restoring functions map...', 2);
			$json = $source->read( $map_json );
			self::$map = json_decode($json, true);
		}

		// build map of who is calling who
		//
		if (empty( self::$map ))
		{
			$all = $storage->getFunctions();
			foreach ($all as $name)
			{
				$entity = $storage->readFunction($name);

				if (empty($entity->fileOrigin))
				{
					Verbose::log("No file: {$entity->name}()", 3);
					continue;
				}

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

					self::$map[ $func ][ $name ][] = "{$call[0]}:{$call[1]}";
				}
			}
		}

		// save a map
		//
		$source->write(
			$map_json,
			json_encode( self::$map, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES )
			);
	}
}
