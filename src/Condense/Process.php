<?php

namespace ShrinkPress\Build\Condense;

use ShrinkPress\Build\Project;

class Process
{
	protected $tasks = array();

	function __construct()
	{
		$this->tasks[] = new Task\Wipe;
		$this->tasks[] = new Task\Start;
		$this->tasks[] = new Task\Functions;
	}

	function condense(
		Project\Source $source,
		Project\Storage\StorageAbstract $storage
		)
	{
		foreach ($this->tasks as $task)
		{
			$task->condense($source, $storage);
		}
	}
}
