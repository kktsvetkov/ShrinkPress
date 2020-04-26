<?php

namespace ShrinkPress\Reframe\Evolve;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class HasCalls extends NodeVisitorAbstract
{
	public $exitOnFirstMatch = false;

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
		if (Core::isCoreFunction( $functionName ))
		{
			return;
		}

		$this->result[] = $functionName;

		if ($this->exitOnFirstMatch)
		{
			// return NodeTraverser::STOP_TRAVERSAL;
			return 2;
		}
	}
}
