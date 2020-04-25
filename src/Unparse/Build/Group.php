<?php

namespace ShrinkPress\Reframe\Unparse\Build;

use ShrinkPress\Reframe\Index;
use ShrinkPress\Reframe\Unparse;
use ShrinkPress\Reframe\Assist;

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
			Assist\Verbose::log( 'Task: ' . get_class($task), 4);
			$task->build($source, $index);
		}
	}
}
