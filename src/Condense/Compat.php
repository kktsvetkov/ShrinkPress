<?php

namespace ShrinkPress\Build\Condense;

class Compat
{
	use Instance;

	const compatibility_php = Composer::vendors . '/shrinkpress/compatibility.php';

	protected $functions = array();

	function addFunction($old, $new)
	{
		$this->functions[] = array($old, $new);
	}

	function addClass()
	{

	}

	function addGlobals()
	{

	}

	function dump()
	{

	}
}
