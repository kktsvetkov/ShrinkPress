<?php

namespace ShrinkPress\Build\Index;

use ShrinkPress\Build\Assist;
use ShrinkPress\Build\Entity;

class Index_Stash extends Index_Abstract
{
	protected $umbrella;

	function __construct(Assist\Umbrella $umbrella)
	{
		$this->umbrella = $umbrella;
	}

	protected function stashFilename($entityType)
	{
		$entityType = (string) $entityType;
		return 'register.' . $entityType . '.json';
	}

	protected function stashEntityFilename($entityType, $entityName)
	{
		$entityType = (string) $entityType;
		$entityName = (string) $entityName;
		return $entityType . '/' . $entityName . '.json';
	}

	protected function stashLoad($stashFilename)
	{
		$stashFilename = (string) $stashFilename;
		if (!$this->umbrella->exists( $stashFilename ))
		{
			return false;
		}

		if (!$json = $this->umbrella->read( $stashFilename ))
		{
			return false;
		}

		return (array) json_decode($json, true);
	}

	function getFiles()
	{
		return $this->stashLoad( $this->stashFilename('files') );
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
		return $this->stashLoad( $this->stashFilename('classes') );
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
		return $this->stashLoad( $this->stashFilename('includes') );
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
		return $this->stashLoad( $this->stashFilename('globals') );
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
		return $this->stashLoad( $this->stashFilename('functions') );
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
		return false;
	}
}
