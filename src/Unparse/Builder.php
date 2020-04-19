<?php

namespace ShrinkPress\Reframe\Unparse;

class Builder extends Build\Group
{
	function __construct()
	{
		$this->addTask( new Build\Start );
	}
}
