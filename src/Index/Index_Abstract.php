<?php

namespace ShrinkPress\Build\Index;

use ShrinkPress\Build\Entity;

abstract class Index_Abstract
{
	abstract function getFiles();
	abstract function readFile( $filename );
	abstract function writeFile( Entity\Files\File_Entity $entity );

	abstract function getClasses();
	abstract function readClass( $className );
	abstract function writeClass( Entity\Classes\Class_Entity $entity );

	abstract function getIncludes();
	abstract function readIncludes( $includedFile );
	abstract function writeInclude( Entity\Includes\Include_Entity $entity );

	abstract function getGlobals();
	abstract function readGlobal( $globalName );
	abstract function writeGlobal( Entity\Globals\Global_Entity $entity );

	abstract function getFunctions();
	abstract function readFunction( $functionName );
	abstract function writeFunction( Entity\Funcs\Function_Entity $entity );
	function getFunction( $functionName )
	{
		if ($entity = $this->readFunction( $functionName ))
		{
			return $entity;
		}

		return new Entity\Funcs\WordPress_Func( $functionName );
	}

	abstract function readCalls( $functionName );
	abstract function writeCall( Entity\Calls\Call_Entity $entity );

	abstract function readCallbacks( $functionName );
	abstract function writeCallback( Entity\Callbacks\Callback_Entity $entity );

	abstract function clean();
}