<?php

namespace ShrinkPress\Build\Entity\Packages;

use ShrinkPress\Build\Entity;

abstract class Package_Abstract implements Package_Entity
{
	use Entity\Load;

	protected $packageName;

	function __construct($packageName)
	{
		$this->packageName = (string) $packageName;
	}

	function packageName()
	{
		return $this->packageName;
	}

	function addFile(Entity\Files\File_Entity $entity)
	{
		$filename = $entity->filename();
		if (!in_array($filename, $this->files))
		{
			$this->files[] = $filename;
		}
	}

	protected $files = array();
}
