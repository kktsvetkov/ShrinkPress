<?php

namespace ShrinkPress\Evolve;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ReplaceCalls extends NodeVisitorAbstract
{
	public $find = '';

	function __construct($find)
	{
		$this->find = (string) $find;
	}

	public $result = array();

	function beforeTraverse(array $nodes)
	{
		$this->result = array();
	}

	function leaveNode(Node $node)
	{
		if (!$node instanceof Node\Expr\FuncCall)
		{
			return;
		}

		if (!$node->name instanceOf Node\Name)
		{
			return;
		}

		$functionName = (string) $node->name;
		if ($this->find != $functionName)
		{
			return;
		}

		$this->result[] = $node->getLine();
	}

	function afterTraverse(array $nodes)
	{
		rsort($this->result);
	}
}
