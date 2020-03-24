<?php

namespace ShrinkPress\Build\Find;

use PhpParser\Node;
use PhpParser\NodeTraverser;

use ShrinkPress\Build\Project;

class Traverser
{
	protected $traverser;
	protected $visitors = array();

	protected static $instance;

	function __construct()
	{
		$this->visitors[] = new Functions;
		$this->visitors[] = new Calls;
		$this->visitors[] = new Callbacks;
		$this->visitors[] = new Classes;
		$this->visitors[] = new Globals;
		$this->visitors[] = new Includes;

		$this->traverser = new NodeTraverser;

		self::$instance = $this;
	}

	static function traverse(Project\File $file, Project\Storage\StorageAbstract $storage)
	{
		if (!self::$instance)
		{
			new self;
		}

		$nodes = $file->parsed();
		$filename = $file->filename();

		$traverser = self::$instance->traverser;
		$visitors = self::$instance->visitors;

		foreach ($visitors as $visitor)
		{
			$visitor->load( $filename, $storage );

			$traverser->addVisitor( $visitor );
			$traverser->traverse( $nodes );
			$traverser->removeVisitor( $visitor );
		}
	}
}
