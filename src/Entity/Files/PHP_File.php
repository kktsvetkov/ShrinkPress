<?php

namespace ShrinkPress\Build\Entity\Files;

use ShrinkPress\Build\Entity;

class PHP_File Extends File_Abstract
{
	protected $classes = array();

	function addClass(Entity\Classes\Class_Entity $class)
	{
		$this->classes[ $class->className() ] = $class->startLine;
		return $this;
	}

	protected $functions = array();

	function addFunction(Entity\Funcs\Function_Entity $func)
	{
		$this->functions[ $func->functionName() ] = $func->startLine;
		return $this;
	}

	protected $globals = array();

	function addGlobal(Entity\Globals\Global_Entity $global, $line)
	{
		$this->globals[ $global->globalName() ] = (int) $line;
		return $this;
	}
}
