<?php

namespace ShrinkPress\Build\Find;

use PhpParser\Node;
use ShrinkPress\Build\Verbose;

class Callbacks extends Visitor
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

		$node->arg_pos = $arg_pos;
		$node->caller = $caller;

		$this->push($node);
	}

	function push(Node $node)
	{
		$arg_pos = $node->arg_pos;
		$caller = $node->caller;
		$callback = $node->args[ $arg_pos ]->value->value;
		$line = $node->getLine();

		Verbose::log("Callback: {$callback}() at {$this->filename}:{$line}", 3);

		$called = $this->storage->read(
			$this->storage::ENTITY_FUNCTION,
			$callback
			);

		$called->callers[] = array(
			$this->filename, $line, $caller
			);

		$this->storage->write(
			$this->storage::ENTITY_FUNCTION,
			$callback,
			$called->getData()
			);
	}
}
