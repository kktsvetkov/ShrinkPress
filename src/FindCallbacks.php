<?php

namespace ShrinkPress\Build;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class FindCallbacks extends NodeVisitorAbstract
{
	protected $callbacks = [];

	const callback_functions = array(
		'add_filter' => 1,
		'has_filter' => 1,
		'remove_filter' => 1,

		'add_action' => 1,
		'has_action' => 1,
		'remove_action' => 1,
	);

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

		$func_name = $node->name->__toString();

		if (empty(self::callback_functions[ $func_name ]))
		{
			return;
		}

		$arg_pos = self::callback_functions[ $func_name ];
		if (empty($node->args[ $arg_pos ]))
		{
			return;
		}
		$this->callbacks[] = array(
			$node->args[ $arg_pos ]->value->value,
			$node->getStartLine(),
			$func_name
		);
	}

	protected static $instance;
	protected static $traverser;

	static function getCallbacks(array $nodes)
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

		self::$instance->callbacks = array();

		self::$traverser->traverse( $nodes );
		return self::$instance->callbacks;
	}
}
