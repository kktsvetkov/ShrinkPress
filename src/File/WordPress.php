<?php

namespace ShrinkPress\Build\File;

use ShrinkPress\Build\Verbose;

class WordPress extends FileAbstract
{
	protected $classes = array();
	protected $classesRemoved = array();

	protected $functions = array();
	protected $functionsRemoved = array();

	protected $uses = array();

	function isEmpty()
	{
		return false;
	}
}
