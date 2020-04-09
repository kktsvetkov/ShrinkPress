<?php

namespace ShrinkPress\Build\Entity\Files;

use ShrinkPress\Build\Entity;

abstract class File_Abstract implements File_Entity
{
	use Entity\Load;

	protected $filename;

	function __construct($filename)
	{
		$this->filename = (string) $filename;
	}

	function filename()
	{
		return $this->filename;
	}
}
