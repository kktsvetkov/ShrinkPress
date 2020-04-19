<?php

namespace ShrinkPress\Reframe\Index;

use ShrinkPress\Reframe\Entity;

abstract class Index_Abstract
{
	abstract function getFiles();
	abstract function readFile( $filename );
	abstract function writeFile( Entity\Files\File_Entity $entity );

	abstract function getPackages();
	abstract function readPackage( $packageName );
	abstract function writePackage( Entity\Packages\Package_Entity $entity );

	function fullPackageName(Entity\Files\File_Entity $entity)
	{
		if (empty($entity->docPackage))
		{
			return 'Unknown';
		}

		$fullPackageName = $entity->docPackage
			. ($entity->docSubPackage
				? ".{$entity->docSubPackage}"
				: ''
			);

		return $fullPackageName;
	}

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

	abstract function readCalls( Entity\Funcs\Function_Entity $entity );
	abstract function writeCalls( Entity\Funcs\Function_Entity $entity );

	abstract function readCallbacks( Entity\Funcs\Function_Entity $entity );
	abstract function writeCallbacks( Entity\Funcs\Function_Entity $entity );

	abstract function clean();
}
