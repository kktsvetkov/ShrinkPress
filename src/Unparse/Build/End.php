<?php

namespace ShrinkPress\Reframe\Unparse\Build;

class End extends Group
{
	function __construct()
	{
		$this->addTask( new UseClasses );
		$this->addTask( new AutoloadDump );
	}
}