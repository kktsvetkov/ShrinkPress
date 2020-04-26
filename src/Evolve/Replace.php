<?php

namespace ShrinkPress\Reframe\Evolve;

class Replace
{
	protected $wordPressFolder = '';

	function __construct($wordPressFolder)
	{
		$this->wordPressFolder = $wordPressFolder;
	}

	function replaceFunction(array $f, array $m)
	{

	}
}
