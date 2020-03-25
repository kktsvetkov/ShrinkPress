<?php

namespace ShrinkPress\Build\Find;

use PhpParser\Node;
use ShrinkPress\Build\Verbose;
use ShrinkPress\Build\Project\Storage;

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

		$callback = $node->args[ $arg_pos ]->value->value;
		$this->result[ $callback ][] = array(
			$node->getLine(),
			$caller,
			);
	}

	function flush(array $result, Storage\StorageAbstract $storage)
	{
		foreach($result as $callback => $calls)
		{
			$func = $storage->readFunction( $callback );

			foreach ($calls as $call)
			{
				$line = $call[0];
				$inside = $call[1];

				Verbose::log("Callback: {$callback}() at "
					. $this->filename . ':' . $line, 2);

				$func->callers[] = array( $this->filename, $line, $inside);
			}

			$storage->writeFunction( $func );
		}
	}
}
