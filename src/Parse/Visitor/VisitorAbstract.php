<?php

namespace ShrinkPress\Build\Parse\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

use ShrinkPress\Build\Storage;

abstract class VisitorAbstract extends NodeVisitorAbstract
{
	protected $filename;
	protected $storage;

	function load( $filename, Storage\StorageAbstract $storage)
	{
		$this->filename = (string) $filename;
		$this->storage = $storage;
	}

	protected $result = array();

	function beforeTraverse(array $nodes)
	{
		$this->result = array();
	}

	abstract function flush(array $result, Storage\StorageAbstract $storage);

	function afterTraverse(array $nodes)
	{
		if ($this->result)
		{
			$this->flush($this->result, $this->storage);
			$this->result = array();
		}
	}
}
