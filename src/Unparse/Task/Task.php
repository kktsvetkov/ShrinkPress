<?php

namespace ShrinkPress\Build\Unparse\Task;

use ShrinkPress\Build\Index;
use ShrinkPress\Build\Unparse;

interface Task
{
	function build(Unparse\Source $source, Index\Index_Abstract $index);
}
