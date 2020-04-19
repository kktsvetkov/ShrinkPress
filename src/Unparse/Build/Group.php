<?php

namespace ShrinkPress\Reframe\Unparse\Build;

use ShrinkPress\Reframe\Index;
use ShrinkPress\Reframe\Unparse;

class Group implements Task
{
	protected $tasks = array();

	function addTask(Task $task)
	{
		$this->tasks[] = $task;
		return $this;
	}

	function build(Unparse\Source $source, Index\Index_Abstract $index)
	{
		foreach ($this->tasks as $task)
		{
			echo get_class($task), "\n";
			$task->build($source, $index);
		}
	}
}