<?php

namespace ShrinkPress\Reframe\Index;

use ShrinkPress\Reframe\Assist;
use ShrinkPress\Reframe\Entity;

class Index_PDO extends Index_Abstract
{
	protected $pdo;

	function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	static function exists(\PDO $pdo, $key, $value, $table )
	{
		$sql = 'SELECT * FROM ' . $table . ' WHERE ' . $key . ' = ? LIMIT 0, 1';
		$q = $pdo->prepare($sql);
		$q->execute([ (string) $value ]);

		return $q->rowCount()
			? $q->fetch( $pdo::FETCH_ASSOC )
			: array();
	}

	static function all(\PDO $pdo, $key, $table)
	{
		$q = $pdo->query(
			'SELECT ' . $key . ' FROM ' . $table
				. ' GROUP BY ' . $key
				. ' ORDER BY ' . $key
			);

		return $q->fetchAll($pdo::FETCH_COLUMN, 0);
	}

	function getFiles()
	{
		return PDO\Files::all($this->pdo);
	}

	function readFile( $filename )
	{
		return PDO\Files::read($filename, $this->pdo);
	}

	function writeFile( Entity\Files\File_Entity $entity )
	{
		PDO\Files::write($entity, $this->pdo);
		return $this;
	}

	function getPackages()
	{
		return PDO\Files::packages($this->pdo);
	}

	function readPackage( $packageName )
	{
		return PDO\Files::readPackage($packageName, $this->pdo);
	}

	function writePackage( Entity\Packages\Package_Entity $entity )
	{
		// packages are stored in the files table
		//
		return $this;
	}

	function getClasses()
	{
		return PDO\Classes::all($this->pdo);
	}

	function readClass( $className )
	{
		return PDO\Classes::read($className, $this->pdo);
	}

	function writeClass( Entity\Classes\Class_Entity $entity )
	{
		return PDO\Classes::write($entity, $this->pdo);
	}

	function getIncludes()
	{
		return PDO\Includes::all($this->pdo);
	}

	function readIncludes( $includedFile )
	{
		return PDO\Includes::read($includedFile, $this->pdo);
	}

	function writeInclude( Entity\Includes\Include_Entity $entity )
	{
		return PDO\Includes::write($entity, $this->pdo);
	}

	function getGlobals()
	{
		return PDO\Globals::all($this->pdo);
	}

	function readGlobal( $globalName )
	{
		return PDO\Globals::read($globalName, $this->pdo);
	}

	function writeGlobal( Entity\Globals\Global_Entity $entity )
	{
		PDO\Globals::write($entity, $this->pdo);
		return $this;
	}

	function getFunctions()
	{
		return PDO\Functions::all($this->pdo);
	}

	function readFunction( $functionName )
	{
		return PDO\Functions::read($functionName, $this->pdo);
	}

	function writeFunction( Entity\Funcs\Function_Entity $entity )
	{
		PDO\Functions::write($entity, $this->pdo);
		return $this;
	}

	function readCalls( Entity\Funcs\Function_Entity $entity )
	{
		return PDO\Calls::read($entity, $this->pdo);
	}

	function writeCalls( Entity\Funcs\Function_Entity $entity )
	{
		PDO\Calls::write($entity, $this->pdo);
		return $this;
	}

	function readCallbacks( Entity\Funcs\Function_Entity $entity )
	{
		return PDO\Callbacks::read($entity, $this->pdo);
	}

	function writeCallbacks( Entity\Funcs\Function_Entity $entity )
	{
		PDO\Callbacks::write($entity, $this->pdo);
		return $this;
	}

	function clean()
	{
		PDO\Files::clean($this->pdo);
		PDO\Functions::clean($this->pdo);
		PDO\Calls::clean($this->pdo);
		PDO\Callbacks::clean($this->pdo);
		PDO\Classes::clean($this->pdo);
		PDO\Includes::clean($this->pdo);
		PDO\Globals::clean($this->pdo);

	}
}
