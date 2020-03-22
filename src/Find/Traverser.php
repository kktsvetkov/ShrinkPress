<?php

namespace ShrinkPress\Build\Find;

use PhpParser\Node;
use PhpParser\NodeTraverser;

class Traverser
{
	protected static $instance;

	static function traverse(array $nodes, Visitor $visitor )
	{
		if (!self::$instance)
		{
			self::$instance = new NodeTraverser;
		}

		$visitor->clear();
		self::$instance->addVisitor( $visitor );
		self::$instance->traverse( $nodes );
		self::$instance->removeVisitor( $visitor );

		return $visitor->result();
	}
}
