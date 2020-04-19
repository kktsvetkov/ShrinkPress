<?php

namespace ShrinkPress\Build\Unparse;

class Builder extends Build\Group
{
	function __construct()
	{
		$this->addTask( new Build\Start );
	}
}
