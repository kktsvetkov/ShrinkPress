<?php

namespace ShrinkPress\Build\Storage;

use ShrinkPress\Build\Parse\Entity;

abstract class StorageAbstract
{
	abstract function clean();

	abstract function getFunctions();
	abstract function readFunction( $functionName );
	abstract function writeFunction( Entity\WpFunction $entity );

	abstract function readCalls( $functionName );
	abstract function writeCall( Entity\WpCall $call );

	abstract function getClasses();
	abstract function readClass( $className );
	abstract function writeClass( Entity\WpClass $entity );

	abstract function readIncludes( $includedFile );
	abstract function writeInclude( Entity\WpInclude $entity );

	abstract function getGlobals();
	abstract function readGlobal( $globalName );
	abstract function writeGlobal( Entity\WpGlobal $entity );

	abstract function readCallback( $functionName );
	abstract function writeCallback( Entity\WpCallback $entity );
}
