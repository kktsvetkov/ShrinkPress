<?php

namespace ShrinkPress\Build\Find;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;

use ShrinkPress\Build\Storage;

class Traverser
{
	use \ShrinkPress\Build\Assist\Instance;

	protected $traverser;
	protected $visitors = array();

	function __construct()
	{
		$this->visitors[] = new Functions;
		$this->visitors[] = new Calls;
		$this->visitors[] = new Callbacks;
		$this->visitors[] = new Classes;
		$this->visitors[] = new Globals;
		$this->visitors[] = new Includes;

		$this->traverser = new NodeTraverser;
	}

	function traverse( $filename, array $nodes, Storage\StorageAbstract $storage)
	{
		$traverser = $this->traverser;
		foreach ($this->visitors as $visitor)
		{
			$visitor->load( $filename, $storage );

			$traverser->addVisitor( $visitor );
			$traverser->traverse( $nodes );
			$traverser->removeVisitor( $visitor );
		}
	}

	protected static $parser;

	function parse($code)
	{
		if (empty(static::$parser))
		{
			static::$parser = (new ParserFactory)
				->create(ParserFactory::PREFER_PHP7);
		}

		return static::$parser->parse($code);
	}
}
