<?php

namespace ShrinkPress\Reframe\Evolve;

use PhpParser\Node;

class HasHooks extends VisitorAbstract
{
	const callback_functions = array(
		'add_filter' => 1,
		'has_filter' => 1,
		'remove_filter' => 1,

		'add_action' => 1,
		'has_action' => 1,
		'remove_action' => 1,
	);

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

		$caller = $node->name->__toString();
		if (empty(self::callback_functions[ $caller ]))
		{
			return;
		}

		$arg_pos = self::callback_functions[ $caller ];
		if (empty($node->args[ $arg_pos ]))
		{
			return;
		}

		if (!$node->args[ $arg_pos ]->value instanceof Node\Scalar\String_)
		{
			return;
		}

		$callback = $node->args[ $arg_pos ]->value->value;
		if (Core::isCoreFunction($callback))
		{
			return;
		}

		return $this->push($callback);
	}
}
