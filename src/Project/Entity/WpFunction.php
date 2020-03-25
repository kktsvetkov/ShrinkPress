<?php

namespace ShrinkPress\Build\Project\Entity;

use PhpParser\Node;
use PhpParser\NodeTraverser;

class WpFunction implements WpEntity
{
	public $name;

	public $fileOrigin;

	public $startLine;
	public $endLine;

	public $isPrivate = false;

	public $docComment;
	public $docCommentLine;

	public $callers = [];

	public $classNamespace;
	public $className;
	public $classMethod;
	public $classFile;

	function __construct($name, array $data = [])
	{
		$this->name = $name;

		if ($data)
		{
			$this->load($data);
		}
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

	// temporary
	public $code;
}
