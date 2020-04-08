<?php

namespace ShrinkPress\Build\Unparse;

use ShrinkPress\Build\Storage;
use ShrinkPress\Build\Source;

class Builder
{
	protected $tasks = array();

	function __construct()
	{
		// restore original WordPress,
		// delete new files
		//
		$this->tasks[] = new Task\Wipe;

		// write down .gitignore and .gitattributes
		//
		$this->tasks[] = new Task\DotGit;

		// write down composer.json and do
		// the initial dumpautoload
		//
		$this->tasks[] = new Task\CreateComposer;

		// insert the "vendor/autoload.php" include
		//
		$this->tasks[] = new Task\PlantComposer;

		// build conversion maps
		//
		$this->tasks[] = new Task\PackagesMap;

		// $this->tasks[] = new Task\SortFunctions;
		// $this->tasks[] = new Task\ReplaceFunctions;

		// delete files which are now
		// empty after the conversion
		//
		$this->tasks[] = new Task\CleanEmptyIncludes;

		// both "pluggable.php" and "pluggable-deprecated.php"
		// will have empty "IF (function_exists(...)) .... ENDIF"
		// statements in them from the moved functions
		//
		$this->tasks[] = new Task\CleanPluggable;

		// put the functions, classes and globalvars
		// maps into a plugin, "shrinkpress_migration"
		//
		$this->tasks[] = new Task\CreateMigrationPlugin;
	}

	function build(Source $source, Storage\StorageAbstract $storage)
	{
		foreach ($this->tasks as $task)
		{
			ECHO GET_CLASS($task), "\n";
			$task->build($source, $storage);
		}
	}
}
