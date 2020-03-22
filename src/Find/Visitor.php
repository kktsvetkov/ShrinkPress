<?php

namespace ShrinkPress\Build\Find;

use PhpParser\NodeVisitorAbstract;

abstract class Visitor extends NodeVisitorAbstract
{
	protected $result = array();

	function result()
	{
		return $this->result;
	}

	function clear()
	{
		$this->result = array();
	}

	function push(array $match)
	{
		$this->result[] = $match;
	}
}
