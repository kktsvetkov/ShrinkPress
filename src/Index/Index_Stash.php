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

		// restore ?
		//
		$registers = array('files', 'functions');
		foreach ($registers as $entityType)
		{
			if ($keys = $this->stashLoad( $entityType . '.json' ))
			{
				$this->keys[ $entityType ] = $keys;
			}
		}
	}

	protected $keys = [];

	protected function stashEntityFilename($entityType, $entityName)
	{
		$entityType = (string) $entityType;
		$entityName = (string) $entityName;

		if (!isset($this->keys[ $entityType ]))
		{
			$this->keys[ $entityType ] = array();
		}

		if (empty($this->keys[ $entityType ][ $entityName ]))
		{
			$this->keys[ $entityType ][ $entityName ] = time();
		}

		return $entityType . '/' . $entityName . '.json';
	}

	protected function stashLoad($stashFilename)
	{
		$stashFilename = (string) $stashFilename;
		if (!$this->umbrella->exists( $stashFilename ))
		{
			return [];
		}

		if (!$json = $this->umbrella->read( $stashFilename ))
		{
			return [];
		}

		return (array) json_decode($json, true);
	}

	const json_encode_options = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;

	protected function stashSave($entityType, $entityName, $entity)
	{
		$entityType = (string) $entityType;
		$entityName = (string) $entityName;

		$stashEntityFilename = $this->stashEntityFilename($entityType, $entityName);

		$data = $entity->jsonSerialize();
		$data[':class'] = get_class($entity);

		$this->umbrella->write(
			$stashEntityFilename,
			json_encode( $data, self::json_encode_options )
			);

		$this->umbrella->write(
			$entityType . '.json',
			json_encode( $this->keys[ $entityType ], self::json_encode_options )
			);

		return $this;
	}

	function getFiles()
	{
		return $this->stashLoad( 'files.json' );
	}

	function readFile( $filename )
	{
		$data = $this->stashLoad( $this->stashEntityFilename('files', $filename) );
		if (!empty($data[':class']))
		{
			$entity = new $data[':class']( $filename );
		} else
		{
			$entity = Entity\Files\WordPress_PHP::factory( $filename );
		}

		return $entity->load( $data );
	}

	function writeFile( Entity\Files\File_Entity $entity )
	{
		return $this->stashSave( 'files', $entity->filename(), $entity);
	}

	function getPackages()
	{
		return $this->stashLoad( 'packages.json' );
	}

	function readPackage( $packageName )
	{
		$data = $this->stashLoad(
			$this->stashEntityFilename('packages', $packageName)
			);
		if (!empty($data[':class']))
		{
			$entity = new $data[':class']( $packageName );
		} else
		{
			$entity = new Entity\Packages\WordPress_Package( $packageName );
		}

		return $entity->load( $data );
	}

	function writePackage( Entity\Packages\Package_Entity $entity )
	{
		return $this->stashSave( 'packages', $entity->packageName(), $entity);
	}

	function getClasses()
	{
		return $this->stashLoad( 'classes.json' );
	}

	function readClass( $className )
	{
		$data = $this->stashLoad(
			$this->stashEntityFilename('classes', $className)
			);
		if (!empty($data[':class']))
		{
			$entity = new $data[':class']( $className );
		} else
		{
			$entity = new Entity\Classes\WordPress_Class( $className );
		}

		return $entity->load( $data );
	}

	function writeClass( Entity\Classes\Class_Entity $entity )
	{
		return $this->stashSave( 'classes', $entity->className(), $entity);
	}

	function getIncludes()
	{
		return $this->stashLoad( 'includes.json' );
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
		return $this->stashLoad( 'globals.json' );
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
		return $this->stashLoad( 'functions.json' );
	}

	function readFunction( $functionName )
	{
		$data = $this->stashLoad(
			$this->stashEntityFilename('functions', $functionName)
			);
		if (!empty($data[':class']))
		{
			$entity = new $data[':class']( $functionName );
		} else
		{
			$entity = new Entity\Funcs\WordPress_Func( $functionName );
		}

		return $entity->load( $data );
	}

	function writeFunction( Entity\Funcs\Function_Entity $entity )
	{
		return $this->stashSave( 'functions', $entity->functionName(), $entity);
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
