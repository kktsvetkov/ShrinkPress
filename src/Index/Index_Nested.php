<?php

namespace ShrinkPress\Build\Index;

use ShrinkPress\Build\Entity;

class Index_Nested extends Index_Abstract
{
	protected $nested = array();

	function addNested(Index_Abstract $index)
	{
		$this->nested[] = $index;
	}

	protected function nestedFirst($call, $arg1 = null)
	{
		foreach ($this->nested as $index)
		{
			if (!is_callable([$index, $call]))
			{
				throw new \UnexpectedValueException(
					"Not able to call {$call}() in "
						. get_class($index)
				);
			}

			if ($result = $index->$call($arg1))
			{
				return $result;
			}
		}

		return [];
	}

	protected function nestedAll($call, $arg1 = null)
	{
		foreach ($this->nested as $index)
		{
			if (!is_callable([$index, $call]))
			{
				throw new \UnexpectedValueException(
					"Not able to call {$call}() in "
						. get_class($index)
				);
			}

			$index->$call($arg1);
		}

		return $this;
	}

	function getFiles()
	{
		return $this->nestedFirst( __FUNCTION__ );
	}

	function readFile( $filename )
	{
		return $this->nestedFirst( __FUNCTION__, $filename );
	}

	function writeFile( Entity\Files\File_Entity $entity )
	{
		return $this->nestedAll( __FUNCTION__, $entity );
	}

	function getPackages()
	{
		return $this->nestedFirst( __FUNCTION__ );
	}

	function readPackage( $packageName )
	{
		return $this->nestedFirst( __FUNCTION__, $packageName );
	}

	function writePackage( Entity\Packages\Package_Entity $entity )
	{
		return $this->nestedAll( __FUNCTION__, $entity );
	}

	function getClasses()
	{
		return $this->nestedFirst( __FUNCTION__);
	}

	function readClass( $className )
	{
		return $this->nestedFirst( __FUNCTION__, $className );
	}

	function writeClass( Entity\Classes\Class_Entity $entity )
	{
		return $this->nestedAll( __FUNCTION__, $entity );
	}

	function getIncludes()
	{
		return $this->nestedFirst( __FUNCTION__ );
	}

	function readIncludes( $includedFile )
	{
		return $this->nestedFirst( __FUNCTION__, $includedFile );
	}

	function writeInclude( Entity\Includes\Include_Entity $entity )
	{
		return $this->nestedAll( __FUNCTION__, $entity );
	}

	function getGlobals()
	{
		return $this->nestedFirst( __FUNCTION__ );
	}

	function readGlobal( $globalName )
	{
		return $this->nestedFirst( __FUNCTION__, $globalName );
	}

	function writeGlobal( Entity\Globals\Global_Entity $entity )
	{
		return $this->nestedAll( __FUNCTION__, $entity );
	}

	function getFunctions()
	{
		return $this->nestedFirst( __FUNCTION__ );
	}

	function readFunction( $functionName )
	{
		return $this->nestedFirst( __FUNCTION__, $functionName );
	}

	function writeFunction( Entity\Funcs\Function_Entity $entity )
	{
		return $this->nestedAll( __FUNCTION__, $entity );
	}

	function readCalls( $functionName )
	{
		return $this->nestedFirst( __FUNCTION__, $functionName );
	}

	function writeCall( Entity\Calls\Call_Entity $entity )
	{
		return $this->nestedAll( __FUNCTION__, $entity );
	}

	function readCallbacks( $functionName )
	{
		return $this->nestedFirst( __FUNCTION__, $functionName );
	}

	function writeCallback( Entity\Callbacks\Callback_Entity $entity )
	{
		return $this->nestedAll( __FUNCTION__, $entity );
	}

	function clean()
	{
		return $this->nestedAll( __FUNCTION__ );
	}
}
