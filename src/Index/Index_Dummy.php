<?php

namespace ShrinkPress\Reframe\Index;

use ShrinkPress\Reframe\Entity;

class Index_Dummy extends Index_Abstract
{
	function getFiles()
	{
		return [];
	}

	function readFile( $filename )
	{
		return Entity\Files\WordPress_PHP::factory( $filename );
	}

	function writeFile( Entity\Files\File_Entity $entity )
	{
		return $this;
	}

	function getPackages()
	{
		return [];
	}

	function readPackage( $packageName )
	{
		return new Entity\Packages\WordPress_Package( $packageName );
	}

	function writePackage( Entity\Packages\Package_Entity $entity )
	{
		return $this;
	}

	function getClasses()
	{
		return [];
	}

	function readClass( $className )
	{
		return new Entity\Classes\WordPress_Class( $className );
	}

	function writeClass( Entity\Classes\Class_Entity $entity )
	{
		return $this;
	}

	function getIncludes()
	{
		return [];
	}

	function readIncludes( $includedFile )
	{
		return new Entity\Includes\WordPress_Include( $includedFile );
	}

	function writeInclude( Entity\Includes\Include_Entity $entity )
	{
		return $this;
	}

	function getGlobals()
	{
		return [];
	}

	function readGlobal( $globalName )
	{
		return new Entity\Globals\WordPress_Global( $globalName );
	}

	function writeGlobal( Entity\Globals\Global_Entity $entity )
	{
		return $this;
	}

	function getFunctions()
	{
		return [];
	}

	function readFunction( $functionName )
	{
		return new Entity\Funcs\WordPress_Func( $functionName );
	}

	function writeFunction( Entity\Funcs\Function_Entity $entity )
	{
		return $this;
	}

	function readCalls( Entity\Funcs\Function_Entity $entity )
	{
		return $entity;
	}

	function writeCalls( Entity\Funcs\Function_Entity $entity )
	{
		return $this;
	}

	function readCallbacks( Entity\Funcs\Function_Entity $entity )
	{
		return $entity;
	}

	function writeCallbacks( Entity\Funcs\Function_Entity $entity )
	{
		return $this;
	}

	function clean()
	{

	}
}
