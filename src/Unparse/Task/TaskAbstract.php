<?php

namespace ShrinkPress\Build\Unparse\Task;

use ShrinkPress\Build\Storage;
use ShrinkPress\Build\Source;

abstract class TaskAbstract
{
	abstract function build(Source $source, Storage\StorageAbstract $storage);
}
