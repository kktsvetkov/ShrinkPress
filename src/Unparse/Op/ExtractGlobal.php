<?php

namespace ShrinkPress\Reframe\Unparse\Op;

use ShrinkPress\Reframe\Verbose;

class ExtractGlobal
{
	protected $globalName;
	protected $staticProperty;

	function __construct($globalName, $staticProperty)
	{
		$globalName = (string) $globalName;
		$this->globalName = $globalName;

		$staticProperty = (string) $staticProperty;
		$this->staticProperty = $staticProperty;

		if (!$this->isDeclared($staticProperty))
		{
			$this->declareProperty($staticProperty)
		}
	}

	protected function isDeclared($staticProperty)
	{

	}

	protected function declareProperty($staticProperty)
	{

	}

	function replace($filename, $line)
	{

	}
}
