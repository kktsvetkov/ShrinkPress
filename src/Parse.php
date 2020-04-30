<?php

namespace ShrinkPress\Evolve;

use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class Parse
{
	protected $parser;
	protected $traverser;

	function __construct()
	{
		$this->traverser = new NodeTraverser;
		$this->parser = (new ParserFactory)
			->create(ParserFactory::PREFER_PHP7);
	}

	function parse($code)
	{
		return $this->parser->parse($code);
	}

	function traverse(NodeVisitorAbstract $visitor, array $nodes)
	{
		$this->traverser->addVisitor( $visitor );
		$this->traverser->traverse( $nodes );
		$this->traverser->removeVisitor( $visitor );

		return !empty($visitor->result)
			? $visitor->result
			: null;
	}

}
