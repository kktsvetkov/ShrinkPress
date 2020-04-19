<?php

namespace ShrinkPress\Reframe\Unparse\Build;

use ShrinkPress\Reframe\Index;
use ShrinkPress\Reframe\Unparse;

interface Task
{
	function build(Unparse\Source $source, Index\Index_Abstract $index);
}
