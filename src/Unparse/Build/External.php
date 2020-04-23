<?php

namespace ShrinkPress\Reframe\Unparse\Build;

class External extends Group
{
	function __construct()
	{
		$this->addTask( new External\PhpMailer );
	}
}
