<?php

namespace ShrinkPress\Build\Parse\Visitor;

use PhpParser\Node;
use ShrinkPress\Build\Verbose;
use ShrinkPress\Build\Storage;
use ShrinkPress\Build\Assist\Internal;

class Calls extends VisitorAbstract
{
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

		$func_name = (string) $node->name;
		if (Internal::isInternal( $func_name ))
		{
			return;
		}

		$this->result[ $func_name ][] = array(
			$node->getLine(),
			$this->inside
			);
	}

	function flush(array $result, Storage\StorageAbstract $storage)
	{
		foreach($result as $func_name => $calls)
		{
			$func = $storage->readFunction( $func_name );

			foreach ($calls as $call)
			{
				$line = $call[0];
				$inside = $call[1];

				Verbose::log("Calls {$func_name}() at {$this->filename}:{$line}", 2);

				$func->callers[] = $inside
					? array( $this->filename, $line, $inside)
					: array( $this->filename, $line);
			}

			$storage->writeFunction( $func );
		}
	}
}
