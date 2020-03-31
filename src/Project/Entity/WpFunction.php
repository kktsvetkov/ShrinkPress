<?php

namespace ShrinkPress\Build\Project\Entity;

/**
* Function declaration in WordPress
*/
class WpFunction extends WpEntity
{
	public $fileOrigin = '';

	public $startLine = 0;
	public $endLine = 0;
	public $docCommentLine = 0;

	public $callers = [];

	public $classNamespace = '';
	public $className = '';
	public $classMethod = '';
	public $classFile = '';
}
