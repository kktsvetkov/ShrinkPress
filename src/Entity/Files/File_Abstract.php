<?php

namespace ShrinkPress\Reframe\Entity\Files;

use ShrinkPress\Reframe\Entity;

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
