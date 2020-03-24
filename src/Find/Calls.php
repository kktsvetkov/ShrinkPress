<?php

namespace ShrinkPress\Build\Find;

use PhpParser\Node;
use ShrinkPress\Build\Verbose;

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

		$this->push($node);
	}

	function push(Node $node)
	{
		$line = $node->getLine();

		Verbose::log("Calls {$node->name}() at {$this->filename}:{$line}", 2);

		$call = $this->storage->read(
			$this->storage::ENTITY_FUNCTION,
			(string) $node->name
			);

		$call->callers[] = $this->inside
			? array( $this->filename, $line, $this->inside)
			: array( $this->filename, $line);

		$this->storage->write(
			$this->storage::ENTITY_FUNCTION,
			(string) $node->name,
			$call->getData()
			);
	}
}
