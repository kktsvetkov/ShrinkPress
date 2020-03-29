<?php

namespace ShrinkPress\Build\Find;

use PhpParser\Node;
use PhpParser\NodeTraverser;

use ShrinkPress\Build\Project\Storage;

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
}
