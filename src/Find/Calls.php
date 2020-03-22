<?php

namespace ShrinkPress\Build\Find;

use PhpParser\Node;

class Calls extends Visitor
{
	protected $internal = [];

	function __construct()
	{
		$internal = get_defined_functions()['internal'];
		$this->internal = array_flip($internal);
	}

	protected $inside;

	function enterNode(Node $node)
	{
		if ($node instanceof Node\Stmt\Function_)
		{
			$this->inside = (string) $node->name;
		}
	}

	function leaveNode(Node $node)
	{
		if ($node instanceof Node\Stmt\Function_)
		{
			$this->inside = null;
		}

		if (!$node instanceof Node\Expr\FuncCall)
		{
			return;
		}

		if (!$node->name instanceOf Node\Name)
		{
			return;
		}

		$func_name = $node->name->__toString();
		if (!empty($this->internal[ $func_name ]))
		{
			return;
		}

		$this->push(
			array(
				$func_name,
				$node->getStartLine()
				) + ($this->inside
					? array(2 => $this->inside)
					: array())
			);
	}
}
