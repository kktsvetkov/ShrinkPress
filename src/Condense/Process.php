<?php

namespace ShrinkPress\Build\Condense;

use ShrinkPress\Build\Project\Storage;
use ShrinkPress\Build\Source;

class Process
{
	protected $tasks = array();

	protected $source;

	protected $storage;

	function __construct(Source $source, Storage\StorageAbstract $storage)
	{
		$this->source = $source;
		$this->storage = $storage;

		$this->tasks[] = new Task\Wipe;
		$this->tasks[] = new Task\Start;

		// $this->tasks[] = new Task\FunctionsMap;
		$this->tasks[] = new Task\SortFunctions;
		$this->tasks[] = new Task\ReplaceFunctions;

		$this->tasks[] = new Task\UseNamespaces;
	}

	function condense()
	{
		foreach ($this->tasks as $task)
		{
			$task->condense($this->source, $this->storage);
		}
	}
}
