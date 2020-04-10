<?php

namespace ShrinkPress\Build\Entity\Classes;

use ShrinkPress\Build\Entity;

abstract class Class_Abstract implements Class_Entity
{
	use Entity\Load;

	protected $className;

	function __construct($className)
	{
		$this->className = (string) $className;
	}

	function className()
	{
		return $this->className;
	}

	public $extends;

	public $filename;
	public $docCommentLine;
	public $startLine;
	public $endLine;
}
