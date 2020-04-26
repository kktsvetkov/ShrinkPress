<?php

namespace ShrinkPress\Reframe\Evolve;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class HasHooks extends NodeVisitorAbstract
{
	const callback_functions = array(
		'add_filter' => 1,
		'has_filter' => 1,
		'remove_filter' => 1,

		'add_action' => 1,
		'has_action' => 1,
		'remove_action' => 1,
	);

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

		$this->result[] = $callback;

		if ($this->exitOnFirstMatch)
		{
			// return NodeTraverser::STOP_TRAVERSAL;
			return 2;
		}
	}
}
