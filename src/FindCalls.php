<?php

namespace ShrinkPress\Build;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class FindCalls extends NodeVisitorAbstract
{
	protected $calls = [];

	function leaveNode(Node $node)
	{
		if ($node instanceof Node\Expr\FuncCall)
		{
			$this->calls[] = array(
				$node->name->__toString(),
				$node->getStartLine()
			);
		}
	}

	protected static $instance;
	protected static $traverser;

	static function getCalls(Node $node)
	{
		if (!self::$instance)
		{
			self::$instance = new self;
		}

		if (!self::$traverser)
		{
			self::$traverser = new NodeTraverser;
			self::$traverser->addVisitor( self::$instance );
		}


		self::$instance->calls = array();
		self::$traverser->traverse( $node->getStmts() );
		return self::$instance->calls;
	}
}
