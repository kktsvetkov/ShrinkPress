<?php

namespace ShrinkPress\Build\Entity\Files;

use ShrinkPress\Build\Entity;

class PHP_File Extends File_Abstract
{
	public $size = 0;

	protected $classes = array();

	function addClass(Entity\Classes\Class_Entity $class)
	{
		$class->filename = $this->filename();
		$this->classes[ $class->className() ] = $class->startLine;

		$entity_classes_register = Entity\Register\Classes::instance();
		$entity_classes_register->addClass($class)->save();
	}
}
