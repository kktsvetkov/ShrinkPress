<?php

namespace ShrinkPress\Build\Index;

use ShrinkPress\Build\Assist;
use ShrinkPress\Build\Entity;

class Index_PDO extends Index_Abstract
{
	protected $pdo;

	function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}


	function getFiles()
	{
		return [];
	}

	function readFile( $filename )
	{
		return false;
	}

	function writeFile( Entity\Files\File_Entity $entity )
	{

	}

	function getClasses()
	{
		return [];
	}

	function readClass( $className )
	{
		return false;
	}

	function writeClass( Entity\Classes\Class_Entity $entity )
	{

	}

	function getIncludes()
	{
		return [];
	}

	function readIncludes( $includedFile )
	{
		return false;
	}

	function writeInclude( Entity\Includes\Include_Entity $entity )
	{

	}

	function getGlobals()
	{
		return [];
	}

	function readGlobal( $globalName )
	{
		return false;
	}

	function writeGlobal( Entity\Globals\Global_Entity $entity )
	{

	}

	function getFunctions()
	{
		return [];
	}

	function readFunction( $functionName )
	{
		return false;
	}

	function writeFunction( Entity\Funcs\Function_Entity $entity )
	{

	}

	function readCalls( $functionName )
	{
		return false;
	}

	function writeCall( Entity\Calls\Call_Entity $entity )
	{

	}

	function readCallbacks( $functionName )
	{
		return false;
	}

	function writeCallback( Entity\Callbacks\Callback_Entity $entity )
	{

	}

	function clean()
	{

	}
}
