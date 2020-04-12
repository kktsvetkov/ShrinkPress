<?php

namespace ShrinkPress\Build\Entity\Files;

use ShrinkPress\Build\Entity;

class PHP_File Extends File_Abstract
{
	protected $classes = array();

	function addClass(Entity\Classes\Class_Entity $class)
	{
		$class->filename = $this->filename();
		$this->classes[ $class->className() ] = $class->startLine;

		return $this;
	}

	protected $functions = array();

	function addFunction(Entity\Funcs\Function_Entity $func)
	{
		$func->filename = $this->filename();
		$this->functions[ $func->functionName() ] = $func->startLine;

		return $this;
	}
}
