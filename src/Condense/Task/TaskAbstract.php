<?php

namespace ShrinkPress\Build\Condense\Task;

use ShrinkPress\Build\Storage;
use ShrinkPress\Build\Source;

abstract class TaskAbstract
{
	abstract function condense(
		Source $source,
		Storage\StorageAbstract $storage
		);
}
