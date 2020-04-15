<?php

namespace ShrinkPress\Build\Parse\Visitor;

use PhpParser\Node;
use ShrinkPress\Build\Assist;
use ShrinkPress\Build\Entity;
use ShrinkPress\Build\Index;

class Calls extends Visitor_Abstract
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
		if (Assist\Internal::isInternal( $functionName ))
		{
			return;
		}

		$this->result[ $functionName ][] = $node->getLine();
	}

	function flush(array $result, Index\Index_Abstract $index)
	{
		foreach($result as $functionName => $lines)
		{
			$entity = $index->readFunction( $functionName );

			foreach ($lines as $line)
			{
				Assist\Verbose::log(
					"Calls {$functionName}() at {$this->filename}:{$line}",
					2);
				$entity->addCall($this->filename, $line);
			}

			$index->writeFunction($entity);
		}
	}
}
