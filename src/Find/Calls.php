<?php

namespace ShrinkPress\Build\Find;

use PhpParser\Node;
use ShrinkPress\Build\Verbose;
use ShrinkPress\Build\Project\Storage;

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

		$func_name = (string) $node->name;
		if (!empty($this->internal[ $func_name ]))
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
			$func = $storage->read(
				$this->storage::ENTITY_FUNCTION,
				$func_name
				);

			foreach ($calls as $call)
			{
				$line = $call[0];
				$inside = $call[1];

				Verbose::log("Calls {$func_name}() at {$this->filename}:{$line}", 2);

				$func->callers[] = $inside
					? array( $this->filename, $line, $inside)
					: array( $this->filename, $line);
			}

			$storage->write(
				$this->storage::ENTITY_FUNCTION,
				$func_name,
				$func->getData()
				);
		}
	}
}
