<?php

namespace ShrinkPress\Build\Parse\Entity;

class WpClass extends EntityAbstract
{
	public $className = '';
	public $namespace = '';

	function __construct( $className )
	{
		$this->className = (string) $className;
	}

	public $end = 0;

	public $docCommentLine = 0;

	function __toString()
	{
		return (string) $this->className;
	}
}
