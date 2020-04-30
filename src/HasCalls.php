<?php

namespace ShrinkPress\Evolve;

use PhpParser\Node;

class HasCalls extends VisitorAbstract
{
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

		return $this->push($functionName);
	}
}
