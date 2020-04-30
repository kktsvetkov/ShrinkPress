<?php

namespace ShrinkPress\Evolve;

use PhpParser\Node;

class HasGlobalsStatements extends VisitorAbstract
{
	protected $is_global = false;

	function enterNode(Node $node)
	{
		if ($node instanceof Node\Stmt\Global_)
		{
			$this->is_global = true;
		}
	}

	function leaveNode(Node $node)
	{
		if ($node instanceof Node\Stmt\Global_)
		{
			$this->is_global = false;
		}

		if ($this->is_global)
		{
			if ($node instanceof Node\Expr\Variable)
			{
				$globalName = (string) $node->name;
				if (!in_array($globalName, HasSuperGlobals::ignore))
				{
					$this->push( $globalName );
				}
			}
		}
	}
}
