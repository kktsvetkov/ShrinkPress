<?php

namespace ShrinkPress\Build\Entity\Files;

use ShrinkPress\Build\Entity;

class PHP_File Extends File_Abstract
{
	protected $lines = array();

	protected function addToLines($name, $entity)
	{
		$count = count($this->lines);
		if (!empty($entity->startLine))
		{
			$this->lines[ $entity->startLine ] = 'starts:' . $name;
		}

		if (!empty($entity->endLine))
		{
			$this->lines[ $entity->endLine ] = 'ends:' . $name;
		}

		if (!empty($entity->docCommentLine))
		{
			$this->lines[ $entity->docCommentLine ] = 'doccoment:' . $name;
		}

		if ($count < count($this->lines))
		{
			ksort($this->lines);
		}
	}

	protected $classes = array();

	function addClass(Entity\Classes\Class_Entity $class)
	{
		$this->addToLines($class->className(), $class);

		$class->filename = $this->filename();
		$this->classes[ $class->className() ] = $class->startLine;

		$entity_classes_register = Entity\Register\Classes::instance();
		$entity_classes_register->addClass($class)->save();
	}
}
