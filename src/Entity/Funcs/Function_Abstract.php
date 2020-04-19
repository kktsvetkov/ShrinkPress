<?php

namespace ShrinkPress\Reframe\Entity\Funcs;

use ShrinkPress\Reframe\Entity;

abstract class Function_Abstract implements Function_Entity
{
	use Entity\Load;

	protected $functionName;

	function __construct($functionName)
	{
		$this->functionName = (string) $functionName;
	}

	function functionName()
	{
		return $this->functionName;
	}

	public $filename;
	public $docCommentLine;
	public $startLine;
	public $endLine;

	protected $calls = array();

	function addCall($filename, $line)
	{
		$call = array((string) $filename, (int) $line);
		$this->calls[ "{$call[0]}:{$call[1]}" ] = $call;

		return $this;
	}

	function getCalls()
	{
		return $this->calls;
	}
}
