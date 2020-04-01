<?php

namespace ShrinkPress\Build\Condense\Task;

use ShrinkPress\Build\Project;
use ShrinkPress\Build\Source;

abstract class TaskAbstract
{
	abstract function condense(
		Source $source,
		Project\Storage\StorageAbstract $storage
		);
}
