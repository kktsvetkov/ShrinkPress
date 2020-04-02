<?php

namespace ShrinkPress\Build\Parse\Entity;

/**
* Function declaration in WordPress
*/
class WpFunction extends EntityAbstract
{
	public $functionName = '';

	function __construct( $functionName )
	{
		$this->functionName = (string) $functionName;
	}

	public $end = 0;

	public $docCommentLine = 0;

	public $callers = [];

	public $classNamespace = '';
	public $className = '';
	public $classMethod = '';
	public $classFile = '';

	function __toString()
	{
		return (string) $this->functionName;
	}
}
