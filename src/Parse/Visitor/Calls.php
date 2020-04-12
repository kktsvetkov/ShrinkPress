<?php

namespace ShrinkPress\Build\Parse\Visitor;

use PhpParser\Node;
use ShrinkPress\Build\Verbose;
use ShrinkPress\Build\Storage;
use ShrinkPress\Build\Entity;
use ShrinkPress\Build\Assist\Internal;
use ShrinkPress\Build\Parse\Entity\WpCall;

class Calls extends VisitorAbstract
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
		if (Internal::isInternal( $functionName ))
		{
			return;
		}

		$this->result[ $functionName ][] = $node->getLine();
	}

	function flush(array $result, Storage\StorageAbstract $storage)
	{
		foreach($result as $functionName => $lines)
		{
			$entity = new WpCall( $functionName );
			$entity->filename = $this->filename;

			$entity_func = Entity\Register\Functions::instance()->getFunction($functionName);

			foreach ($lines as $line)
			{
				Verbose::log("Calls {$functionName}() at {$this->filename}:{$line}", 2);

				$entity_func->addCall($this->filename, $line);

				$entity->line = $line;
				$storage->writeCall( $entity );
			}
		}
	}
}
