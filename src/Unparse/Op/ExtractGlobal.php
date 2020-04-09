<?php

namespace ShrinkPress\Build\Unparse\Op;

use ShrinkPress\Build\Verbose;

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
