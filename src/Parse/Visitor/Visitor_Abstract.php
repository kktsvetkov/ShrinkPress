<?php

namespace ShrinkPress\Build\Parse\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

use ShrinkPress\Build\Index;
use ShrinkPress\Build\Entity;

abstract class Visitor_Abstract extends NodeVisitorAbstract
{
	protected $filename;
	protected $index;

	function load( $filename, Index\Index_Abstract $index)
	{
		$this->filename = (string) $filename;
		$this->index = $index;
	}

	protected $result = array();

	function beforeTraverse(array $nodes)
	{
		$this->result = array();
	}

	abstract function flush(array $result, Index\Index_Abstract $index);

	function afterTraverse(array $nodes)
	{
		if ($this->result)
		{
			$this->flush($this->result, $this->index);
			$this->result = array();
		}
	}
}
