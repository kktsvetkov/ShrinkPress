<?php

namespace ShrinkPress\Build\Project\Entity;

use PhpParser\Node;
use PhpParser\NodeTraverser;

class WpFunction implements WpEntity
{
	public $name;

	public $file;	// origin

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
