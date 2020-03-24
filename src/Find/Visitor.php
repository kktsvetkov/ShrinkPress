<?php

namespace ShrinkPress\Build\Find;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use ShrinkPress\Build\Project\Storage;

abstract class Visitor extends NodeVisitorAbstract
{
	protected $filename;
	protected $storage;

	function load( $filename, Storage\StorageAbstract $storage)
	{
		$this->filename = (string) $filename;
		$this->storage = $storage;
	}

	abstract function push(Node $node);
}
