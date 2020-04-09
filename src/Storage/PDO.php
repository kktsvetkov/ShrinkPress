<?php

namespace ShrinkPress\Build\Storage;

use ShrinkPress\Build\Parse\Entity;

class PDO extends StorageAbstract
{
	protected $pdo;

	function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	//
	// Functions
	//

	function readFunction($functionName)
	{
		$entity = new Entity\WpFunction( (string) $functionName );

		if ($found = PDO\WpFunction::exists ( $entity, $this->pdo ))
		{
			foreach ($found as $k => $v)
			{
				$entity->$k = $v;
			}
		}

		return $entity;
	}

	function writeFunction(Entity\WpFunction $entity)
	{
		return PDO\WpFunction::write($entity, $this->pdo);
	}

	function getFunctions()
	{
		return PDO\WpFunction::all($this->pdo);
	}

	//
	// Calls
	//

	function readCalls($functionName)
	{
		return PDO\WpCall::read($functionName, $this->pdo);
	}

	function writeCall( Entity\WpCall $call )
	{
		return PDO\WpCall::write($call, $this->pdo);
	}

	//
	// Classes
	//

	function readClass($className)
	{
		$entity = new Entity\WpClass( (string) $className );

		if ($found = PDO\WpClass::exists ( $entity, $this->pdo ))
		{
			foreach ($found as $k => $v)
			{
				$entity->$k = $v;
			}
		}

		return $entity;
	}

	function writeClass(Entity\WpClass $entity)
	{
		return PDO\WpClass::write($entity, $this->pdo);
	}

	function getClasses()
	{
		return PDO\WpClass::all($this->pdo);
	}

	//
	// Includes
	//

	function readIncludes( $includedFile )
	{
		return PDO\WpInclude::read($includedFile, $this->pdo);
	}

	function writeInclude( Entity\WpInclude $entity )
	{
		return PDO\WpInclude::write($entity, $this->pdo);
	}

	//
	// Globals
	//

	function getGlobals()
	{
		return PDO\WpGlobal::all($this->pdo);
	}

	function readGlobal( $globalName )
	{
		return PDO\WpGlobal::read($globalName, $this->pdo);
	}

	function writeGlobal( Entity\WpGlobal $entity )
	{
		return PDO\WpGlobal::write($entity, $this->pdo);
	}

	//
	// Callbacks
	//

	function readCallbacks( $functionName )
	{
		return PDO\WpCallback::read($functionName, $this->pdo);
	}

	function writeCallback( Entity\WpCallback $entity )
	{
		return PDO\WpCallback::write($entity, $this->pdo);
	}

	function clean()
	{
		PDO\WpFunction::clean($this->pdo);
		PDO\WpCall::clean($this->pdo);
		PDO\WpClass::clean($this->pdo);
		PDO\WpInclude::clean($this->pdo);
		PDO\WpGlobal::clean($this->pdo);
		PDO\WpCallback::clean($this->pdo);
	}
}
