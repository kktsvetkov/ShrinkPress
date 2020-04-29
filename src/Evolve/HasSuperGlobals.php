<?php

namespace ShrinkPress\Reframe\Evolve;

use PhpParser\Node;

class HasSuperGlobals extends VisitorAbstract
{
	const ignore = array(
		'HTTP_RAW_POST_DATA',
		'PHP_SELF',
		);

	function leaveNode(Node $node)
	{
		if (!$node instanceof Node\Expr\ArrayDimFetch)
		{
			return;
		}

		if (!$node->var instanceof Node\Expr\Variable)
		{
			return;
		}

		if ('GLOBALS' == (string) $node->var->name)
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

			if (in_array($globalName, self::ignore))
			{
				return;
			}

			return $this->push( $globalName );
		}
	}
}
