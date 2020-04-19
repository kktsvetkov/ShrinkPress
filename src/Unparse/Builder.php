<?php

namespace ShrinkPress\Build\Unparse;

class Builder extends Task\Group
{
	function __construct()
	{
		$this->addTask( new Task\Start\Task );
	}
}
