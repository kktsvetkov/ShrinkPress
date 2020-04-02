<?php

namespace ShrinkPress\Build\Unparse;

use ShrinkPress\Build\Storage;
use ShrinkPress\Build\Source;

class Builder
{
	protected $tasks = array();

	protected $source;

	protected $storage;

	function __construct(Source $source, Storage\StorageAbstract $storage)
	{
		$this->source = $source;
		$this->storage = $storage;

		// $this->tasks[] = new Task\Wipe;
		// $this->tasks[] = new Task\Start;
		//
		// // $this->tasks[] = new Task\FunctionsMap;
		// $this->tasks[] = new Task\SortFunctions;
		// $this->tasks[] = new Task\ReplaceFunctions;
		//
		// $this->tasks[] = new Task\UseNamespaces;
	}

	function build()
	{
		foreach ($this->tasks as $task)
		{
			$task->condense($this->source, $this->storage);
		}
	}
}
