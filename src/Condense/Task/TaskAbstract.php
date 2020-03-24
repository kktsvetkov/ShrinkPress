<?php

namespace ShrinkPress\Build\Condense\Task;

use ShrinkPress\Build\Project;

abstract class TaskAbstract
{
	abstract function condense(
		Project\Source $source,
		Project\Storage\StorageAbstract $storage
		);
}
