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

		// // $this->tasks[] = new Task\FunctionsMap;
		// $this->tasks[] = new Task\SortFunctions;
		// $this->tasks[] = new Task\ReplaceFunctions;
		//
		// $this->tasks[] = new Task\UseNamespaces;

		// $this->tasks[] = new Task\CreateMigrationPlugin;
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
