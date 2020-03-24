<?php

namespace ShrinkPress\Build\Find;

use PhpParser\Node;
use ShrinkPress\Build\Verbose;

class Functions extends Visitor
{
	function leaveNode(Node $node)
	{
		if (!$node instanceof Node\Stmt\Function_)
		{
			return;
		}

		$this->push($node);
	}

	function push(Node $node)
	{
		Verbose::log(
			"Function: {$node->name}() at {$this->filename}:"
				. $node->getStartLine(),
			1);

		$func = $this->storage->read(
			$this->storage::ENTITY_FUNCTION,
			(string) $node->name
			);

		$func->file = $this->filename;

		$func->startLine = $node->getStartLine();
		$func->endLine = $node->getEndLine();

		// is it private ?
		//
		if ($docComment = $node->getDocComment())
		{
			$func->isPrivate = (false !== strpos(
				$docComment->__toString(),
				'@access private'
				));
		}

		// tmp only
		$p = new \PhpParser\PrettyPrinter\Standard;
		$func->code = $p->prettyPrint([$node]);
		// ^

		$this->storage->write(
			$this->storage::ENTITY_FUNCTION,
			(string) $node->name,
			$func->getData()
			);
	}
}
