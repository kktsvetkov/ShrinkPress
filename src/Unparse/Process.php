<?php

namespace ShrinkPress\Build\Unparse;

use ShrinkPress\Build\Storage;
use ShrinkPress\Build\Source;

class Builder
{
	protected $tasks = array();

	function __construct()
	{
		// $this->tasks[] = new Task\Wipe;
		// $this->tasks[] = new Task\Start;
		//
		// // $this->tasks[] = new Task\FunctionsMap;
		// $this->tasks[] = new Task\SortFunctions;
		// $this->tasks[] = new Task\ReplaceFunctions;
		//
		// $this->tasks[] = new Task\UseNamespaces;
	}

	function build(Source $source, Storage\StorageAbstract $storage)
	{
		foreach ($this->tasks as $task)
		{
			$task->condense($source, $storage);
		}
	}
}
