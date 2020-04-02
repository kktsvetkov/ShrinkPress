<?php

namespace ShrinkPress\Build\Project\Entity;

/**
* Function declaration in WordPress
*/
class WpFunction extends EntityAbstract
{
	public $functionName = '';

	public $end = 0;

	public $docCommentLine = 0;

	public $callers = [];

	public $classNamespace = '';
	public $className = '';
	public $classMethod = '';
	public $classFile = '';
}
