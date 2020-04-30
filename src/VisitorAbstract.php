<?php

namespace ShrinkPress\Evolve;

use PhpParser\NodeVisitorAbstract;

abstract class VisitorAbstract extends NodeVisitorAbstract
{
	public $exitOnFirstMatch = false;

	public $result = array();

	function beforeTraverse(array $nodes)
	{
		$this->result = array();
	}

	function push($result)
	{
		$this->result[] = $result;

		if ($this->exitOnFirstMatch)
		{
			// return NodeTraverser::STOP_TRAVERSAL;
			return 2;
		}

		return;
	}

	function afterTraverse(array $nodes)
	{
		if (!$this->exitOnFirstMatch)
		{
			rsort($this->result);
		}
	}
}
