<?php

namespace ShrinkPress\Reframe\Evolve;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class FindCalls extends NodeVisitorAbstract
{
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

		$this->result = $node;

		// return NodeTraverser::STOP_TRAVERSAL;
		return 2;
	}
}
