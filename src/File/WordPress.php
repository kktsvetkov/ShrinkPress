<?php

namespace ShrinkPress\Build\File;

use ShrinkPress\Build\Verbose;

class WordPress extends FileAbstract
{
	protected $classes = array();
	protected $classesRemoved = array();
	function addClass(array $class)
	{
		$this->classes[] = $class;
	}

	protected $functions = array();
	protected $functionsRemoved = array();

	protected $globals = array();
	protected $globalsRemoved = array();
	function addGlobal(array $global)
	{
		$this->globals[] = $global;
	}

	protected $includes = array();
	protected $includesRemoved = array();
	function addInclude(array $include)
	{
		$this->includes[] = $include;
	}

	protected $uses = array();

	function isEmpty()
	{
		return false;
	}
}
