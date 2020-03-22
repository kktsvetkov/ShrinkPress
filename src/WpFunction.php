<?php

namespace ShrinkPress\Build;

use PhpParser\Node;
use PhpParser\NodeTraverser;

class WpFunction implements WpEntity
{
	protected $name;

	public $file;
	public $startLine;
	public $endLine;
	public $isPrivate = false;
	public $callers = [];

	function __construct($name, array $data = [])
	{
		$this->name = $name;

		if ($data)
		{
			$this->load($data);
		}
	}

	function getFile()
	{
		$prefix = 'function/' . substr($this->name, 0, 2) . '/';
		return $prefix . $this->name . '.php';
	}

	function getData()
	{
		return get_object_vars($this);
	}

	function load(array $data)
	{
		foreach ($data as $k => $v)
		{
			if (property_exists($this, $k))
			{
				$this->$k = $v;
			}
		}
	}

	static function fromNode(Node $node)
	{
		$func = new self( $node->name->__toString() );

		$func->startLine = $node->getStartLine();
		$func->endLine = $node->getEndLine();

		// is it private ?
		//
		if ($docComment = $node->getDocComment())
		{
			$func->isPrivate = (false !== strpos(
				$docComment->__toString(),
				'@access private'
				));
		}

		return $func;
	}

	// temporary
	public $code;
	public $guts;
}
