<?php

namespace ShrinkPress\Build;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class FindCalls extends NodeVisitorAbstract
{
	protected $internal = [];

	function __construct()
	{
		$internal = get_defined_functions()['internal'];
		$this->internal = array_flip($internal);
	}

	protected $calls = [];

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

		$this->calls[] = array(
			$func_name,
			$node->getStartLine()
			) + ($this->inside
				? array(2 => $this->inside)
				: array());
	}

	protected static $instance;
	protected static $traverser;

	static function getCalls(array $nodes)
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
		self::$traverser->traverse( $nodes );
		return self::$instance->calls;
	}
}
