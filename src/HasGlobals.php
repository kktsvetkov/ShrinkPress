<?php

namespace ShrinkPress\Evolve;

use PhpParser\Node;

class HasGlobals extends VisitorAbstract
{
	const ignore = array(
		'HTTP_RAW_POST_DATA',
		'PHP_SELF',
		);

	function leaveNode(Node $node)
	{
		if ($node instanceof Node\Expr\ArrayDimFetch)
		{
			if ($node->var instanceof Node\Expr\Variable)
			{
				if ('GLOBALS' == (string) $node->var->name)
				{
					return $this->global_array_element($node);
				}
			}
		} else

		if ($node instanceof Node\Stmt\Global_)
		{
			foreach ($node->vars as $global)
			{
				$this->push( (string) $global->name );
			}
			return $this->global_statement($node);
		}
	}

	function global_statement(Node\Stmt\Global_ $node)
	{
		foreach ($node->vars as $global)
		{
			$this->push( (string) $global->name );
		}
	}

	function global_array_element(Node\Expr\ArrayDimFetch $node)
	{
		$globalName = '';
		if ($node->dim instanceof Node\Expr\Variable)
		{
			$globalName = (string) $node->dim->name;
		} else
		if ($node->dim instanceof Node\Scalar\String_)
		{
			$globalName = (string) $node->dim->value;
		}

		return $this->push( $globalName );
	}
}
